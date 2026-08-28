<?php

namespace App\Http\Controllers;

use App\Events\FollowRequestEvent;
use App\Events\UserFollowEvent;
use App\Http\Resources\FollowRequestResource;
use App\Models\FollowRequest;
use App\Models\User;
use App\Services\UserServices;
use Illuminate\Http\Request;

class FollowRequestController extends Controller
{
    //
    public function follow (UserServices $userServices , Request $request , User $user){
        //
        $currentUser = $request->user();
        $this->authorize('view',[$currentUser ,$user]);
        if($currentUser->is($user)){
            return response()->json([
                'message' => 'You cannot follow yourself.'
            ],422);
        }

        if($currentUser->following()->whereKey($user->id)->exists()){
            return response()->json([
                'message' => "Alreday following $user->name"
            ],200);
        }
        //Checking if the user status account is public
        if(!$user->is_private){
            $res = $userServices->followPubliAccount($currentUser ,$user);
            $message = !empty($res['attached']) ? "you started following $user->name" : "you are unfollowed $user->name";
            if(!empty($res['attached'])){
                broadcast(new UserFollowEvent($currentUser ,$user))->toOthers();
            }
            return response()->json([
                'message' => $message
            ],200);
        }
        //if the user status account is private
        //if the request already exist cancel the follow request
        $pendingRequest = FollowRequest::where('sender_id',$currentUser->id)->where('receiver_id',$user->id)->first();
        if($pendingRequest){
            $pendingRequest->delete();
            return response()->json([
                'message' => 'your follow request cancelled'
            ],200);
        }
        //sent the follow request
        $userServices->sendFollowRequest($currentUser ,$user);
        broadcast(new FollowRequestEvent($user ,$currentUser))->toOthers();
        return response()->json([
            'message' => 'you sent follow request'
        ],201);

    }

    public function acceptFollowRequests(Request $request ,FollowRequest $follow_request){
        //
        $currentUser = $request->user();
        $this->authorize('update',[$currentUser ,$follow_request]);
        $currentUser->followers()->syncWithoutDetaching($follow_request->sender_id);
        event(new UserFollowEvent($follow_request->sender , $currentUser));
        $follow_request->delete();
        return response()->json([
            'message' => "{$follow_request->sender->name} started following you"
        ]);
    }

    public function rejectFollowRequests(Request $request ,FollowRequest $follow_request){
        //
        $currentUser = $request->user();
        $this->authorize('update',[$currentUser ,$follow_request]);
        $follow_request->delete();
        return response()->json([
            'message' => 'The follow request has been rejected.'
        ]);
    }

    public function followRequests(Request $request){
        //
        $follow_requests = $request->user()->load('followRequests');

        return response()->json([
            'follow_requests' => FollowRequestResource::collection($follow_requests->followRequests),
        ],200);
    }

    public function changeAccountStatus(Request $request)
    {
        $user = $request->user();
        $user->is_private = !$user->is_private;
        $user->save();

        return response()->json([
            'message' => 'The status of your account is ' . ($user->is_private ? 'private' : 'public'),
        ]);
    }
}
