<?php

namespace App\Services;

use App\Events\UserFollowEvent;
use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserServices{

    public function followPubliAccount(User $currentUser , User $user){
        $res = $currentUser->following()->toggle($user->id);
            if(!empty($res["attached"])){
                event(new UserFollowEvent($currentUser,$user));
        }
        return $res;
    }

    public function sendFollowRequest(User $currentUser , User $user){
        $follow_request = FollowRequest::updateOrCreate([
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id],
            ['status' => 'pending']
        );
        return $follow_request;
    }
    public function insertUser(array $validatedInfo ,?UploadedFile $image):User
    {
        //
        $imagePath = null;
        if($image){
            $imagePath = $image->store('photos','public');
        }
        $user = User::create([
            'name' => $validatedInfo['name'],
            'email' => $validatedInfo['email'],
            'password'=> Hash::make($validatedInfo['password']),
            'image' => $imagePath
        ]);

        return $user;
    }

    public function UpdateUser(array $validatedInfo , User $user , ?UploadedFile $image): User
    {
        //
        if(empty($validatedInfo['password'])){
            unset($validatedInfo['password']);
        }else{
            $validatedInfo['password'] = Hash::make($validatedInfo['password']);
        }
        if($image){
            if($user->image){
                Storage::disk('public')->delete($user->image);
            }
            $validatedInfo['image'] = $image->store('photos', 'public');
        }
        $user->update($validatedInfo);

        return $user;
    }
}