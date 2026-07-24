<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\HashTag;
use App\Models\Post;
use Illuminate\Http\Request;

class HashTagsController extends Controller
{
    //
    public function posts(string $hashtag){
        //
        $posts = Post::whereHas('hashtags',function($q) use($hashtag){
            $q->where('hashtag',$hashtag);
        })->withCount('likes')
          ->orderBy('likes_count','desc')
          ->get();
          
        return response()->json([
            'posts' => PostResource::collection($posts)
        ],200);
    }
}
