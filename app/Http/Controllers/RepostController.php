<?php

namespace App\Http\Controllers;

use App\Events\UserRepostedEvent;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RepostController extends Controller
{
    //
    public function repost(PostService $postService ,Request $request , Post $post){
        //
        $currentUser = $request->user();
        $this->authorize('view',$post);

        $repost = $postService->repostPost($currentUser ,$post);

        return response()->json([
            'message' => $repost ? 'Post reposted successfully.' : 'Post removed from reposted posts.'
        ],200);
    }

    public function repostsUsers(Post $post){
        //
        $$usersDb = $post->reposts()->get();

        $key = 'post:'.$post->id.':reposts';
        $usersIds = Redis::sMembers($key);
        $redisUsers = User::whereIn('id' ,$usersIds)->get();

        $allUsers = $usersDb->merge($redisUsers)
                            ->unique('id')
                            ->values();

        return response()->json([
            'reposted_users' => UserResource::collection($allUsers)
        ],200);
    }
}
