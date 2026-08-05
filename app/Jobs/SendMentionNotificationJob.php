<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Notifications\MentionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMentionNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user , public Post $post , public User $targetdUser)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        if($this->user->id === $this->targetdUser->id)return;
        $this->user->notify(new MentionNotification($this->user , $this->post , $this->targetdUser));
    }
}
