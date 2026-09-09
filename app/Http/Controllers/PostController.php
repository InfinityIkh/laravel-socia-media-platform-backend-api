<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReportResource;
use App\Jobs\CountPostViewsJob;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Report;
use App\Services\PostServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function report(ReportRequest $request , Post $post){
        //
        $this->authorize('view',$post);

        if((int)$post->user_id === (int)$request->user()->id){
            return response()->json([
                'message' => 'You cannot Report your Post.'
            ],422);
        }

        $attributes = $request->validated();
        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'raison' => $attributes['raison'],
            'reportable_type' => Post::class,
            'reportable_id' => $post->id
        ]);

        return response()->json([
            'report' => new ReportResource($report)
        ]);
    }

    public function index(Request $request)
    {
        $posts = Post::visible($request->user())->paginate();
        return response()->json([
            'posts' => PostResource::collection($posts),
            'pagination' => [
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage()
            ]
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostServices $postService , PostRequest $request)
    {
        //
        $validated = $request->validated();
        $user = $request->user();
        $images = $request->file('images');

        $post = $postService->insertPost($validated , $user , $images);

        return response()->json([
            'post' => new PostResource($post)
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request , Post $post)
    {
        //
        $this->authorize('view',$post);

        Redis::sAdd('post:'.$post->id.':viewers',$request->user()->id);

        $post = $post->load('user',
                            'views',
                            'saves',
                            'likes',
                            'reposts',
                            'comments');

        return response()->json([
            'post' => new PostResource($post),
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostServices $postService , PostRequest $request, Post $post)
    {
        //
        $validated = $request->validated();

        $this->authorize('update',$post);
        $images = $request->file('images');

        $updatedPost = $postService->updatePost($validated , $post , $images);

        return response()->json([
            'updated_post'=> new PostResource($updatedPost)
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
