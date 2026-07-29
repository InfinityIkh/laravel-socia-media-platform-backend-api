<?php

namespace App\Policies;

use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FollowRequestsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FollowRequest $followRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FollowRequest $followRequest): bool
    {
        return $user->id === $followRequest->receiver_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FollowRequest $followRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FollowRequest $followRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FollowRequest $followRequest): bool
    {
        return false;
    }
}
