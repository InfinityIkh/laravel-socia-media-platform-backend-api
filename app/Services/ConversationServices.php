<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;

class ConversationServices
{
    public function createConversation(array $requestValidate ,User $authUser){
        //
        $conversation = Conversation::create([
            'type' => $requestValidate['type'],
        ]);

        $userIds = collect($requestValidate['user_ids'])
            ->push($authUser->id)
            ->unique()
            ->values();
        $conversation->users()->attach($userIds);
        $conversation->load('users');
        
        return $conversation;
    }
}