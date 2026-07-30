<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    //
    public function index(Request $request){
        //
        $followingIds = $request->user()->following->pluck('users.id');
        $followingPosts = Post::with(['user'])
                                ->visible($request->user())
                                ->whereIn('user_id',$followingIds)
                                ->latest()
                                ->get();

        return response()->json([
            'posts' => PostResource::collection($followingPosts)
        ]);
    }

    public function mostLiked(Request $request){
        $posts = Post::with(['user','likes'])
                       ->visible($request->user())
                       ->withCount('likes')
                       ->orderBy('likes_count','desc')
                       ->get();
                       
        return response()->json([
            'posts' => PostResource::collection($posts)
        ], 200);
    }

    public function mostWatched(Request $request){
        $posts = Post::with(['user','views'])
                       ->visible($request->user())
                       ->withCount('views')
                       ->orderBy('views_count' , 'desc')
                       ->get();

        return response()->json([
            'posts' => PostResource::collection($posts)
        ], 200);
    }
}
