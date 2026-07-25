<?php

use App\Events\UserMentionEvent;
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

        //
        $hashtagsIds = $this->gettingHashtagAndInsert($validatedInfo);
        $post->hashtags()->sync($hashtagsIds);

        //
        $this->mentionUsers($validatedInfo , $post->user , $post);

        //
        if($images){
            $this->uploadImages($images , $post);
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

        //
        if(!$images){
            unset($validatedInfo['images']);
        }else{
            $this->deleteImages($post);
            $this->uploadImages($images , $post);
        }

        //
        $hashtagsIds = $this->gettingHashtagAndInsert($validatedInfo);
        $post->hashtags()->sync($hashtagsIds);

        //
        $this->mentionUsers($validatedInfo , $post->user , $post);

        return $post->load('images','user');
    }

    public function gettingHashtagAndInsert(array $validatedInfo){
        //
        preg_match_all('/#(\w+)/',$validatedInfo['body'],$matches);
        $hashtagsNames = array_unique(array_map('strtolower', $matches[1] ?? []));

        $hashtagsIds = collect($hashtagsNames)->map(function ($hashtag) {
            return HashTag::firstOrCreate([
                'hashtag' => $hashtag
            ])->id;
        })->toArray();

        return $hashtagsIds;
    }

    public function mentionUsers(array $validatedInfo , User $user , Post $post){
        //
        preg_match_all('/@(\w+)/',$validatedInfo['body'] ?? '',$matchesBody);
        preg_match_all('/@(\w+)/',$validatedInfo['title'] ?? '',$matchesTitle);
        $mentionNamesTitile = array_unique(array_map('strtolower', $matchesTitle[1] ?? []));
        $mentionNamesBody = array_unique(array_map('strtolower', $matchesBody[1] ?? []));

        $mentions = array_merge($mentionNamesBody , $mentionNamesTitile);
        foreach($mentions as $targetedUser){
            event(new UserMentionEvent($user , $post , $targetedUser));
        }
    }

    public function uploadImages(array $images , Post $post){
        //
        foreach($images as $image){
            $imagePath = $image->store('photos','public');
            $post->images()->create([
                'image_path' => $imagePath
            ]);
        }
    }

    public function deleteImages(Post $post){
        foreach($post->images as $image){
            Storage::disk('public')->delete($image->image_path);
        }
        $post->images()->delete();
    }
}