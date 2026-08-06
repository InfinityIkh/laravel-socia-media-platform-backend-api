<?php

namespace App\Services;

use App\Events\UserLikedEvent;
use App\Events\UserMentionEvent;
use App\Jobs\SendLikeNotificationJob;
use App\Jobs\SendRepostNotificationJob;
use App\Models\HashTag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
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

    public function likePost(User $currentUser ,Post $post){
        //
        $likesKey = 'post:'.$post->id.':likes';
        $unlikesKey = 'post:'.$post->id.':unlikes';
        $likedUser = $post->likes()->where('user_id',$currentUser->id)->exists() || Redis::sismember($likesKey , $currentUser->id);
        if($likedUser){
            Redis::sAdd($unlikesKey ,$currentUser->id);
            Redis::srem($likesKey ,$currentUser->id);
            $like = false;
        }else{
            Redis::sAdd($likesKey ,$currentUser->id);
            Redis::srem($unlikesKey ,$currentUser->id);
            $like = true;
            SendLikeNotificationJob::dispatch($currentUser ,$post);
        }
        return $like;
    }

    public function savePost(User $currentUser ,Post $post){
        $savedKey = 'post:'.$post->id.':saves';
        $unsavedKey = 'post:'.$post->id.':unsaves';
        $userSave = $post->saves()->where('user_id',$currentUser->id)->exists() || Redis::sismember($savedKey ,$currentUser->id);
        if($userSave){
            Redis::sAdd($unsavedKey ,$currentUser->id);
            Redis::srem($savedKey ,$currentUser->id);
            $save = false;
        }else{
            Redis::sAdd($savedKey ,$currentUser->id);
            Redis::srem($unsavedKey ,$currentUser->id);
            $save = true;
        }
        return $save;
    }

    public function repostPost(User $currentUser ,Post $post){
        $repostKey = 'post:'.$post->id.':reposts';
        $unrepostKey = 'post:'.$post->id.':unreposts';
        $userSave = $post->reposts()->where('user_id',$currentUser->id)->exists() || Redis::sismember($repostKey ,$currentUser->id);
        if($userSave){
            Redis::sAdd($unrepostKey ,$currentUser->id);
            Redis::srem($repostKey ,$currentUser->id);
            $repost = false;
        }else{
            Redis::sAdd($repostKey ,$currentUser->id);
            Redis::srem($unrepostKey ,$currentUser->id);
            $repost = true;
            SendRepostNotificationJob::dispatch($currentUser ,$post);
        }
        return $repost;
    }

}