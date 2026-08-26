<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowRequestController;
use App\Http\Controllers\HashTagsController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\RepostController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//
Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/{type}/login',[AuthController::class,'login']);

//Using Laravel Sanctum For Authentification
Route::middleware('auth:sanctum')->group(function(){
    //
    Route::get('/profile',function (Request $request){
        return response()->json([
            'user' => new UserResource($request->user())
        ],200);
    });
    //
    Route::post('/auth/{type}/logout',[AuthController::class,'logout']);
    //Crud Operations For User
    Route::get('/users/{user}',[UserController::class,'show']);
    Route::put('/users/{user}',[UserController::class,'update']);
    //Crud Operations For User only Admin Can Do
    Route::middleware('admin')->group(function(){
        Route::delete('/users/{user}',[UserController::class,'destroy']);
        Route::get('/users',[UserController::class,'index']);
        Route::post('/users',[UserController::class,'store']);
    });
    //
    Route::get('/users/search',[UserController::class,'search']);
    Route::get('/users/{user}/followers',[UserController::class,'followers']);
    Route::get('/users/{user}/following',[UserController::class,'following']);
    //Crud Operations For Post And Other Functionality
    Route::apiResource('posts',PostController::class);
    Route::get('posts/{post}/likes',[LikesController::class,'likedUsers']);
    Route::post('posts/{post}/like',[LikesController::class,'like']);
    Route::get('posts/{post}/reposts',[RepostController::class,'repostsUsers']);
    Route::post('posts/{post}/repost',[RepostController::class,'repost']);
    Route::get('posts/{post}/saves',[SaveController::class,'saves']);
    Route::post('posts/{post}/save',[SaveController::class,'save']);
    //Crud Operations For Comment
    Route::get('/posts/{post}/comments',[CommentController::class,'index']);
    Route::get('comments/{comment}',[CommentController::class,'show']);
    Route::post('/posts/{post}/comments',[CommentController::class,'store']);
    Route::put('/posts/{post}/comments/{comment}',[CommentController::class,'update']);
    Route::delete('/posts/{post}/comments/{comment}',[CommentController::class,'destroy']);
    //Crud Operations For Post
    Route::get('/posts/{post}/comments/{comment}/replies', [ReplyController::class, 'index']);
    Route::post('/posts/{post}/comments/{comment}/replies', [ReplyController::class, 'store']);
    Route::get('/replies/{reply}', [ReplyController::class, 'show']);
    Route::put('/replies/{reply}', [ReplyController::class, 'update']);
    Route::delete('/replies/{reply}', [ReplyController::class, 'destroy']);
    //feed
    Route::get('/feed',[FeedController::class,'index']);
    Route::get('/feed/most-liked',[FeedController::class,'mostLiked']);
    Route::get('/feed/most-watched',[FeedController::class,'mostWatched']);
    //Users-notifications
    Route::get('/notifications', [NotificationController::class, 'getAllNotifications']);
    Route::get('/notifications/unread', [NotificationController::class, 'getUnReadNotifications']);
    Route::get('/notifications/read', [NotificationController::class, 'getReadNotifications']);
    Route::put('/notifications/mark-as-read', [NotificationController::class, 'readingUserNotifications']);
    //Suggestions
    Route::get('/friends-of-friends',[UserController::class , 'friendsOfFriends']);
    Route::get('/may-you-knows',[UserController::class , 'mayYouKnow']);
    //HashTags
    Route::get('/{hashtag}/posts',[HashTagsController::class,'posts']);
    //reports
    Route::post('/users/{user}/report',[UserController::class,'report']);
    Route::post('/posts/{post}/report',[PostController::class,'report']);
    //blocks
    Route::post('/users/{user}/block',[UserController::class,'block']);
    Route::get('/user/blocked_users',[UserController::class,'blocked_users']);
    //follow_requests
    Route::post('/users/{user}/follow',[FollowRequestController::class,'follow']);
    Route::put('/follow-requests/{id}/accept',[FollowRequestController::class,'acceptFollowRequests']);
    Route::put('/follow-requests/{id}/reject',[FollowRequestController::class,'rejectFollowRequests']);
    Route::get('/follow-requests',[FollowRequestController::class,'followRequests']);
    Route::put('/change-status',[FollowRequestController::class,'changeAccountStatus']);
    //story
    Route::get('/stories/me',[StoryController::class ,'userStories']);
    Route::post('stories',[StoryController::class,'store']);
    Route::delete('stories/{story}',[StoryController::class,'destroy']);
    Route::get('stories/{story}',[StoryController::class,'show']);
    Route::get('stories/{story}/viewers',[StoryController::class,'storyViewers']);
    //Conversations
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::post('/conversations/{conversation}/leave', [ConversationController::class, 'leave']);
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy']);
    //Messages
    Route::get('/conversations/{conversation}/messages', [MessagesController::class, 'index']);
    Route::post('/conversations/{conversation}/messages', [MessagesController::class, 'store']);
    Route::patch('/messages/{message}/read', [MessagesController::class, 'markAsRead']);
    Route::delete('/messages/{message}', [MessagesController::class, 'destroy']);
});
