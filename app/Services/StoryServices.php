<?php

namespace App\Services;

use App\Jobs\ProcessStoryImageJob;
use App\Jobs\ProcessvideoJob;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoryServices{
    public function uploadStory(User $user , UploadedFile $file): Story
    {
        //
        $media_path = null;
        $media_type = null;
        $media_type = str_starts_with($file->getMimeType() ,'image/') ? 'image' : 'video';
        $media_path = match($media_type){
            'image' => $file->store('stories/image/original','public'),
            'video' => $file->store('stories/video/original','public')
        };

        $story = Story::create([
            'user_id' => $user->id,
            'media_path' => $media_path,
            'media_type' => $media_type,
            'expires_at' => now()->addDay()
        ]);

        $story->media_type === 'image' ? ProcessStoryImageJob::dispatch($story ,$media_path) : ProcessvideoJob::dispatch($story ,$media_path);

        return $story;
    }

    public function deleteExpiredStories(){
        Story::where('expires_at' ,'<=' ,now())->chunkById(100 ,function($stories){
            $paths = $stories->pluck('media_path')->filter()->all();
            Storage::disk('public')->delete($paths);
            $stories->each->delete();
        });
    }
        //
    //public function updateStory(Story $story ,array $validated , ?UploadedFile $file): Story
    //{
    //    //
    //    if($file){
    //        Storage::disk('public')->delete($story->media_path);
    //        $validated['media_path'] = $file->store('stories','public');
    //        $validated['media_type'] = str_starts_with($file->getMimeType(),'image/') ? 'image' : 'video';
    //    }
    //    $story->update($validated);
//
    //    return $story;
    //}
}