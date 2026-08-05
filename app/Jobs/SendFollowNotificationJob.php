<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\FollowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendFollowNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $follower , public User $followed)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->followed->notify(
            new FollowNotification($this->follower)
        );
    }
}
