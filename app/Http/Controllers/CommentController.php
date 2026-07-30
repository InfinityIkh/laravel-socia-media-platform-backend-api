<?php

namespace App\Http\Controllers;

use App\Events\UserCommentedEvent;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ReportResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function report(ReportRequest $request , Comment $comment){
        //
        $this->authorize('view',$comment);
        if((int)$comment->user_id === (int)$request->user()->id){
            return response()->json([
                'message' => 'You cannot Report your comment.'
            ],422);
        }
        
        $attributes = $request->validated();
        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'raison' => $attributes['raison'],
            'reportable_type' => Comment::class,
            'reportable_id' => $comment->id
        ]);

        return response()->json([
            'report' => new ReportResource($report)
        ]);
    }

    public function index(Request $request ,Post $post)
    {
        //
        $this->authorize('view',[$request->user() ,$post]);
        $comments = $post->comments()->visible($request->user())->with('user')->get();
        return response()->json([
            'comments' => CommentResource::collection($comments)
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request , Post $post)
    {
        //
        $commentInfo = $request->validated();
        $comment = $post->comments()->create([
            'body' => $commentInfo['body'],
            'user_id' => $request->user()->id,
        ]);

        event(new UserCommentedEvent($request->user() , $post));

        return response()->json([
            'comment' => $comment
        ],201);
    }

    /**
     * Display the specified resource.
     */
    //public function show(Request $request , Post $post ,Comment $comment)
    //{
    //    //
    //    $this->authorize('view',[$request->user() ,$post]);
    //    $cmnt = $comment->load('user');
    //    return response()->json([
    //        'comment' => new CommentResource($cmnt)
    //    ],200);
    //}

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        //
        $this->authorize('update',$comment);

        $commentInfo = $request->validated();
        $comment->update($commentInfo);

        return response()->json([
            'updated_comment' => new CommentResource($comment)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
        $this->authorize('delete',$comment);

        $comment->delete();
        return response()->json([
            'message' => 'Comment Deleted Succesfully'
        ],200);
    }
}
