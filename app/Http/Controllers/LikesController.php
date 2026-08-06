<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LikesController extends Controller
{
    public function like(PostService $postService ,Request $request ,Post $post){
        //
        $currentUser = $request->user();
        $this->authorize('view',$post);

        $like = $postService->likePost($currentUser ,$post);

        return response()->json([
            'liked' => (bool) $like,
            'message' => $like ? 'post liked successfully' : 'post unliked successfully'
        ],200);
    }

    public function likedUsers(Post $post){
        //
        $dbUsers = $post->likes()->get();

        $key = 'post:'.$post->id.':likes';
        $usersIds = Redis::sMembers($key);
        $redisUsers = User::whereIn('id' ,$usersIds)->get();

        $allUsers = $dbUsers->merge($redisUsers)
                            ->unique('id')
                            ->values();

        return response()->json([
            'liked_users' => UserResource::collection($allUsers)
        ],200);
    }
}