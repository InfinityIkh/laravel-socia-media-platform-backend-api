<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\HashTag;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json([
            'posts' => PostResource::collection($posts)
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        //
        $validated = $request->validated();
        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        $hashtagsIds = [];
        preg_match_all('/#\w+/',$validated['body'],$matches);
        foreach(array_unique($matches[0]) as $elem){
            $hashtag = HashTag::firstOrCreate([
                'hashtag' => strtolower(ltrim($elem , '#'))
            ]);
            $hashtagsIds = array_merge($hashtagsIds , [$hashtag->id]);
        }
        $post->hashtags()->sync($hashtagsIds);

        return response()->json([
            'post' => new PostResource($post)
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request , Post $post)
    {
        $post = $post->load('user');
        $post->views()->createOrFirst([
            'post_id' => $post->id,
            'user_id' => $request->user()->id
        ]);
        return response()->json([
            'post' => new PostResource($post),
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        //
        $validated = $request->validated();

        $this->authorize('update',$post);

        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->save();

        $hashtagsIds = [];
        preg_match_all('/#\w+/',$validated['body'],$matches);
        foreach(array_unique($matches[0]) as $elem){
            $hashtag = HashTag::firstOrCreate([
                'hashtag' => strtolower(ltrim($elem , '#'))
            ]);
            $hashtagsIds = array_merge($hashtagsIds , [$hashtag->id]);
        }
        $post->hashtags()->sync($hashtagsIds);
        
        return response()->json([
            'updated_post'=> new PostResource($post)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete',$post);

        $post->delete();

        return response()->json([
            'message'=> 'the post deleted successfully'
        ],200);
    }
}
