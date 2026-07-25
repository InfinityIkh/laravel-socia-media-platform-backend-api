<?php

use App\Models\HashTag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PostService{
    public function insertPost(array $validatedInfo , User $user , ?array $images):Post
    {
        //
        $post = $user->posts()->create([
            'title' => $validatedInfo['title'],
            'body' => $validatedInfo['body'],
        ]);

        preg_match_all('/#(\w+)/',$validatedInfo['body'],$matches);
        $hashtagsNames = array_unique(array_map('strtolower',$matches[1] ?? []));
        $hashtagsIds = collect($hashtagsNames)->map(function ($hashtag) {
            return HashTag::firstOrCreate([
                'hashtag' => $hashtag
            ])->id;
        })->toArray();

        $post->hashtags()->sync($hashtagsIds);

        if($images){
            foreach($images as $image){
                $imagePath = $image->store('photos','public');
                $post->images()->create([
                    'image_path' => $imagePath
                ]);
            }
        }
        return $post;
    }

    public function updatePost(array $validatedInfo , Post $post , ?array $images): Post
    {
        //
        $post->update([
            'title' => $validatedInfo['title'],
            'body' => $validatedInfo['body']
        ]);
        if(!$images){
            unset($validatedInfo['images']);
        }else{
            foreach($post->images as $image){
                Storage::disk('public')->delete($image->image_path);
            }
            $post->images()->delete();
            foreach($images as $image){
                $path = $image->store('photos','public');
                $post->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        preg_match_all('/#(\w+)/',$validatedInfo['body'],$matches);
        $hashtagsNames = array_unique(array_map('strtolower', $matches[1] ?? []));

        $hashtagsIds = collect($hashtagsNames)->map(function ($hashtag) {
            return HashTag::firstOrCreate([
                'hashtag' => $hashtag
            ])->id;
        })->toArray();
        
        $post->hashtags()->sync($hashtagsIds);

        return $post->load('images','user');
    }
}