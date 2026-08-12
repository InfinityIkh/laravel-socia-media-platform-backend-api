<?php

namespace App\Services;

use App\Models\Story;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoryServices{
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
    public function uploadStory(User $user , UploadedFile $file): Story
    {
        //
        $media_path = null;
        $media_type = null;
        $media_path = $file->store('images/stories','public');
        $media_type = str_starts_with($file->getMimeType() ,'image/') ? 'image' : 'video';

        $story = Story::create([
            'user_id' => $user->id,
            'media_path' => $media_path,
            'media_type' => $media_type,
            'expires_at' => now()->addDay()
        ]);

        return $story;
    }
}