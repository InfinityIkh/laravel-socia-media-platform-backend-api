<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function (User $user,int $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('follower.{followerId}.followed.{followedId}', function (User $user ,int $followerId,int $followedId) {
    return (int) $user->id === (int) $followedId;
});
Broadcast::channel('sender.{senderId}.follow request receiver.{receiverId}', function (User $user ,int $senderId ,int $receiverId) {
    return (int) $user->id === (int) $receiverId;
});