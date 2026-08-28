<?php

namespace App\Services;

use App\Events\FollowRequestEvent;
use App\Events\UserFollowEvent;
use App\Jobs\ProcessUserImagesJob;
use App\Jobs\SendFollowRequestNotificationJob;
use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserServices{
    //
    public function insertUser(array $validatedInfo ,?UploadedFile $image):User
    {
        //
        $user = User::create([
            'name' => $validatedInfo['name'],
            'email' => $validatedInfo['email'],
            'password'=> Hash::make($validatedInfo['password'])
        ]);

        if($image){
            $imagePath = $image->store('profiles/original','public');
            ProcessUserImagesJob::dispatch($user ,$imagePath);
        }

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
            $path = $image->store('profiles/original', 'public');
            ProcessUserImagesJob::dispatch($user ,$path);
        }
        $user->update($validatedInfo);

        return $user;
    }
}
