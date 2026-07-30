<?php

namespace App\Http\Controllers;

use App\Events\UserRepostedEvent;
use App\Http\Resources\UserResource;
use App\Models\Post;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    //
    public function repost(Request $request , Post $post){
        //
        $this->authorize('view',$post);
        $repost = $post->reposts()->toggle($request->user()->id);
        if(!empty($repost['attached'])){
            event(new UserRepostedEvent($request->user() , $post));
        }
        $message = !empty($repost['attached']) ? 'Post reposted successfully.' : 'Post removed from reposted posts.' ;
        return response()->json([
            'message' => $message
        ],200);
    }

    public function repostsUsers(Request $request ,Post $post){
        //
        $repostsUsers = $post->visible($request->user())->load('reposts');

        return response()->json([
            'reposted_users' => UserResource::collection($repostsUsers->reposts)
        ],200);
    }
}
