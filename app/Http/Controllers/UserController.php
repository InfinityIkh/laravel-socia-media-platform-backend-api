<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\ReportResource;
use App\Http\Resources\UserResource;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserServices;

class UserController extends Controller
{
    //
    public function block(Request $request , User $user){
        //
        $currentUser = $request->user();
        $this->authorize('view',[$currentUser ,$user]);
        if($currentUser->is($user)){
            return response()->json([
                'message' => 'You cannot block yourself.'
            ],422);
        }
        $blockedUser = $currentUser->blockedUsers()->toggle($user->id);
        $currentUser->following()->detach($user->id);
        $currentUser->followers()->detach($user->id);

        return response()->json([
            'blocked' => !empty($blockedUser['attached'])
        ],200);
    }

    public function report(ReportRequest $request , User $user){
        //
        $currentUser = $request->user();
        $this->authorize('view',[$currentUser ,$user]);
        if((int)$user->id === (int)$request->user()->id){
            return response()->json([
                'message' => 'You cannot Report yourself.'
            ],422);
        }

        $attributes = $request->validated();
        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'raison' => $attributes['raison'],
            'reportable_type' => User::class,
            'reportable_id' => $user->id
        ]);

        return response()->json([
            'report' => new ReportResource($report)
        ],201);
    }

    public function search(Request $request){
        //
        $search = $request->input('q');
        $users = User::visible($request->user())->withCount('followers')->orderBy('followers_count' , 'desc')
                                            ->where('name' , 'like' , "%$search%")
                                            ->take(10)
                                            ->get();
        return response()->json([
            'users' => UserResource::collection($users)
        ],200);
    }

    public function friendsOfFriends(Request $request){
        //
        $user = $request->user();
        $followingIds = $user->following->pluck('id');
        $suggestions = User::visible($request->user())->whereHas('following' , function($q) use($followingIds) {
            $q->whereIn('users_followers_following.follower_id' , $followingIds);
        })->where('id' ,'!=', $user->id)
          ->whereNotIn('id' , $followingIds)
          ->take(20)
          ->get();

        return response()->json([
            'suggestions' => UserResource::collection($suggestions)
        ],200);
    }

    public function mayYouKnow(Request $request){
        //
        $user = $request->user();
        $followers = $user->followers();
        $followingIds = $user->following->pluck('id');
        $people = $followers->whereNotIn('id' , $followingIds)->take(20)->get();

        return response()->json([
            'suggestions' => UserResource::collection($people)
        ],200);
    }

    public function followers(User $user){
        //
        $followers = $user->load('followers');
        return response()->json([
            'followers' => UserResource::collection($followers->followers)
        ],200);
    }

    public function following(User $user){
        //
        $followers = $user->load('following');
        return response()->json([
            'following' => UserResource::collection($followers->following)
        ],200);
    }

    public function blocked_users(Request $request){
        //
        $blockedUsers = $request->user()->load('blockedUsers');

        return response()->json([
            'blocked_users' => UserResource::collection($blockedUsers->blockedUsers)
        ],200);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $users = User::visible($request->user())->get();

        return response()->json([
            'users' => UserResource::collection($users)
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserServices $userService , UserRequest $request)
    {
        //
        $userInfo = $request->validated();

        $image = $request->file('image');
        $user = $userService->insertUser($userInfo , $image);

        return response()->json([
            'message' => 'User created.',
            'user' => new UserResource($user)
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request ,User $user)
    {
        //
        $this->authorize('view',[$request->user() ,$user]);

        return response()->json([
            'user' => new UserResource($user)
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserServices $userService , UserRequest $request, User $user)
    {
        //
        $authUser = $request->user();
        $image = $request->file('image');
        $this->authorize('update',[$authUser , $user]);

        $userInfo = $request->validated();

        $user = $userService->UpdateUser($userInfo , $user , $image);

        return response()->json([
            'message' => 'User updated.',
            'updated_user' => new UserResource($user)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , User $user)
    {
        if((int)$request->user()->id !== (int)$user->id && $request->user()->role !== 'admin'){
            return response()->json([
                'message' => 'You do not have permission to Delete this profile.'
            ],403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User Deleted Successfully'
        ],200);
    }
}
