<?php

namespace App\Http\Controllers;

use App\Events\UserFollowEvent;
use App\Http\Resources\UserResource;
use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Http\Request;

class FollowRequestController extends Controller
{
    //
    public function follow (Request $request , User $user){
        //
        $currentUser = $request->user();
        if((int)$user->id === (int)$currentUser->id){
            return response()->json([
                'message' => 'You cannot follow yourself.'
            ],422);
        }

        //Checking if the user status account is public
        if($user->isprivate === 'false'){
            $res = $currentUser->following()->toggle($user->id);
            if(!empty($res["attached"])){
                event(new UserFollowEvent($currentUser,$user));
            }
            return response()->json([
                'follow' => !empty($res['attached'])
            ],202);
        }
        //if the user status account is private
        else{
            //if the request already exist cancel the follow request
            $pendingRequest = FollowRequest::where('sender_id',$currentUser->id)->where('receiver_id',$user->id)->first();
            if($pendingRequest){
                $pendingRequest->delete();
                return response()->json([
                'message' => 'your follow request cancelled'
            ],200);
            }
            //sent the follow request
            FollowRequest::updateOrCreate([
                'sender_id' => $currentUser->id,
                'receiver_id' => $user->id],
                ['status' => 'pending']
            );
            return response()->json([
                'message' => 'you sent follow request'
            ],202);
        }
    }
    
    public function follow_requests(Request $request){
        //
        $follow_requests = $request->user()->load('followRequests');

        return response()->json([
            'follow_requests' => UserResource::collection($follow_requests->followRequests)
        ],200);
    }

    public function changeAccountStatus(Request $request){
        $user = $request->user();
        $user->is_private === 'false' ? $user->is_private = "true" : $user->is_private = "false";
        $user->save();
        $status = $user->is_private === 'false' ? 'public' : 'private';

        return response()->json([
            "message" => "the status of your account is $status"
        ],200);
    }
}
