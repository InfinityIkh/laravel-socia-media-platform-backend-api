<?php

namespace App\Http\Controllers;

use App\Events\UserFollowEvent;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use UserServices;

class UserController extends Controller
{
    //
    public function follow(Request $request , User $User){
        //
        if((int)$User->id === (int)$request->user()->id){
            return response()->json([
                'message' => 'You cannot follow yourself.'
            ],422);
        }
        $user = $request->user()->following()->toggle($User->id);
        if(!empty($user["attached"])){
            event(new UserFollowEvent($request->user(),$User));
        }
        return response()->json([
            'follow' => !empty($user['attached'])
        ],202);
    }

    public function search(Request $request){
        //
        $search = $request->input('q');
        $users = User::withCount('followers')->orderBy('followers_count' , 'desc')
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
        $suggestions = User::whereHas('following' , function($q) use($followingIds) {
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::all();

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
            'user' => new UserResource($user)
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = User::findOrFail($id);

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
            'updated_user' => new UserResource($user)
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , string $id)
    {
        if((int)$request->user()->id !== (int)$id && $request->user()->role !== 'admin'){
            return response()->json([
                'message' => 'You do not have permission to Delete this profile.'
            ],403);
        }
        $user = User::findOrFail($id);
        $user->delete();
        
        return response()->json([
            'message' => 'User Deleted Successfully'
        ],200);
    }
}
