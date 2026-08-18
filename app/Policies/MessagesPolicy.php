<?php

namespace App\Policies;

use App\Models\Messages;
use App\Models\User;

class MessagesPolicy
{
    public function view(User $user, Messages $message): bool
    {
        return $message->conversation
            ->users()
            ->whereKey($user->id)
            ->exists();
    }

    public function delete(User $user, Messages $message): bool
    {
        return $message->user_id === $user->id;
    }
}