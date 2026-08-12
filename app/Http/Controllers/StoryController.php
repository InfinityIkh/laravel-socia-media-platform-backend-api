<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoryRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Models\User;
use App\Services\StoryServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    //
    public function userStories(Request $request){
        //
        $stories = $request->user()
                           ->stories()
                           ->with('user')
                           ->latest()
                           ->get();

        return response()->json([
            'stories' => StoryResource::collection($stories),
        ]);
    }

    public function storyViewers(Story $story){
        //
        $this->authorize('view',$story);
        $users = $story->with('users')->get();

        return response()->json([
            'users' => StoryResource::collection($users)
        ],200);
    }

    public function index(Request $request){
        //
        $currentUser = $request->user();
        $stories = Story::with('user','users')->get();

        return response()->json([
            'stories' => StoryResource::collection($stories)
        ],200);
    }

    public function store(StoryServices $storyService ,StoryRequest $request){
        //
        $currentUser = $request->user();
        $media = $request->file('media');

        $story = $storyService->uploadStory($currentUser ,$media);

        return response()->json([
            'story' => new StoryResource($story->load('user','users'))
        ],201);
    }

    //public function update(StoryServices $storyService ,StoryRequest $request ,Story $story){
    //    //
    //    $this->authorize('update',$story);
    //    $media = $request->file('media');
    //    $validated = $request->validated();
//
    //    $story = $storyService->updateStory($story ,$validated ,$media);
//
    //    return response()->json([
    //        'story' => new StoryResource($story->load('user','users'))
    //    ],200);
    //}

    public function show(Request $request ,Story $story){
        //
        $currentUser = $request->user();
        $this->authorize('poepleCanView',$story);

        $story->users()->syncWithoutDetaching($currentUser->id);

        return response()->json([
            'story' => new StoryResource($story->load('user'))
        ],200);
    }

    public function destroy(Story $story){
        //
        $this->authorize('delete',$story);

        Storage::disk('public')->delete($story->media_path);
        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully'
        ],200);
    }
}
