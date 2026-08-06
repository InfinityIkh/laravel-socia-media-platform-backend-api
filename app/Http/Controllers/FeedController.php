<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    //
    public function index(Request $request){
        //
        $currentUser = $request->user();
        
        $followingPosts = Cache::remember('user:'.$currentUser->id.':following:posts' ,3600 ,function() use($currentUser){
            $followingIds = $currentUser->following()->pluck('users.id');
            $posts = Post::with(['user'])
                                ->visible($currentUser)
                                ->whereIn('user_id',$followingIds)
                                ->latest()
                                ->get();
            return $posts;
        });

        return response()->json([
            'posts' => PostResource::collection($followingPosts)
        ]);
    }

    public function mostLiked(Request $request){
        $currentUser = $request->user();
        $posts = Cache::remember('user:'.$currentUser->id.':following:posts:mostLiked',3600 ,function()use($currentUser){
            $posts = Post::with(['user','likes'])
                       ->visible($currentUser)
                       ->withCount('likes')
                       ->orderBy('likes_count','desc')
                       ->get();
            return $posts;
        });
                       
        return response()->json([
            'posts' => PostResource::collection($posts)
        ], 200);
    }

    public function mostWatched(Request $request){
        $currentUser = $request->user();
        $posts = Cache::remember('user:'.$currentUser->id.':following:posts:mostWatched',3600 ,function()use($currentUser){
            $posts = Post::with(['user','views'])
                       ->visible($currentUser)
                       ->withCount('views')
                       ->orderBy('views_count' , 'desc')
                       ->get();
            return $posts;
        });

        return response()->json([
            'posts' => PostResource::collection($posts)
        ], 200);
    }
}
