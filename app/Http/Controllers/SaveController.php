<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Post;
use Illuminate\Http\Request;

class SaveController extends Controller
{
    //
    public function save(Request $request , Post $post){
        //
        $this->authorize('view',$post);
        $save = $post->saves()->toggle($request->user()->id);
        $message = !empty($save['attached']) ? 'Post saved successfully.' : 'Post removed from saved posts.' ;

        return response()->json([
            'message' => $message
        ],200);
    }

    public function saves(Post $post){
        //
        $postSaves = $post->load('saves');

        return response()->json([
            'users_saved_post' => UserResource::collection($postSaves->saves)
        ],200);
    }
}
