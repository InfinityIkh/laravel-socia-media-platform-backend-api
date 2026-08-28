<?php

namespace App\Services;

use App\Events\FollowRequestEvent;
use App\Events\UserFollowEvent;
use App\Jobs\SendFollowRequestNotificationJob;
use App\Models\FollowRequest;
use App\Models\User;

class FollowServices
{
    //
    public function follow(User $currentUser ,User $user): array
    {
        if($currentUser->is($user)){
            return [
                'message' => 'You cannot follow yourself.'
            ];
        }

        if($currentUser->following()->whereKey($user->id)->exists()){
            return [
                'message' => "Already following $user->name"
            ];
        }
        //Checking if the user status account is public
        if(!$user->is_private){
            $res = $this->followPubliAccount($currentUser ,$user);
            $message = !empty($res['attached']) ? "you started following $user->name" : "you are unfollowed $user->name";
            if(!empty($res['attached'])){
                broadcast(new UserFollowEvent($currentUser ,$user))->toOthers();
            }
            return [
                'message' => $message
            ];
        }
        //if the user status account is private
        //if the request already exist cancel the follow request
        $pendingRequest = FollowRequest::where('sender_id',$currentUser->id)->where('receiver_id',$user->id)->first();
        if($pendingRequest){
            $pendingRequest->delete();
            return [
                'message' => 'your follow request cancelled'
            ];
        }
        //sent the follow request
        $this->sendFollowRequest($currentUser ,$user);
        broadcast(new FollowRequestEvent($user ,$currentUser))->toOthers();
        return [
            'message' => 'you sent follow request'
        ];
    }

    public function acceptFollowRequest(User $currentUser ,FollowRequest $follow_request): array
    {
        //
        $sender = $follow_request->sender;
        $currentUser->followers()->syncWithoutDetaching($sender->id);
        event(new UserFollowEvent($sender ,$currentUser));
        $message = "$sender->name started following you";
        $follow_request->delete();
        return [
            'message' => $message
        ];
    }

    public function changeStatus(User $user): User
    {
        //
        $user->is_private = !$user->is_private;
        $user->save();
        return $user;
    }

    public function reject(FollowRequest $followRequest): void
    {
        //
        $followRequest->delete();
    }

    public function followPubliAccount(User $currentUser , User $user): array
    {
        $res = $currentUser->following()->toggle($user->id);
        if(!empty($res["attached"])){
            event(new UserFollowEvent($currentUser,$user));
        }
        return $res;
    }

    public function sendFollowRequest(User $currentUser , User $user): FollowRequest
    {
        $follow_request = FollowRequest::updateOrCreate([
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id],
            ['status' => 'pending']
        );
        SendFollowRequestNotificationJob::dispatch($user ,$currentUser);
        return $follow_request;
    }
}
