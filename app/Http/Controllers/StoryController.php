<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoryRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Services\StoryServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    //
    public function uploadStory(StoryServices $storyService ,StoryRequest $request){
        //
        $currentUser = $request->user();
        $media = $request->file('media');

        $story = $storyService->uploadStory($currentUser ,$media);

        return response()->json([
            'story' => new StoryResource($story->load('user'))
        ],201);
    }

    public function removeStory(Story $story){
        //
        $this->authorize('delete',$story);

        Storage::disk('public')->delete($story->media_path);
        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully'
        ],200);
    }
}
