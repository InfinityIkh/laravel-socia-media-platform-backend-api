<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use App\Services\PostServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SaveController extends Controller
{
    //
    public function save(PostServices $postService ,Request $request , Post $post){
        //
        $this->authorize('view',$post);
        $currentUser = $request->user();

       $save = $postService->savePost($currentUser ,$post);

        return response()->json([
            'message' => $save ? 'Post saved successfully.' : 'Post removed from saved posts.'
        ],200);
    }

    public function saves(Post $post){
        //
        $usersDb = $post->saves()->get();

        $key = 'post:'.$post->id.':saves';
        $usersIds = Redis::sMembers($key);
        $redisUsers = User::whereIn('id' ,$usersIds)->get();

        $allUsers = $usersDb->merge($redisUsers)
                            ->unique('id')
                            ->values();

        return response()->json([
            'users_saved_post' => UserResource::collection($allUsers)
        ],200);
    }
}
