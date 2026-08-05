<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\FollowRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendFollowRequestNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $receiver , public User $sender)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->receiver->notify(
            new FollowRequestNotification($this->sender)
        );
    }
}
