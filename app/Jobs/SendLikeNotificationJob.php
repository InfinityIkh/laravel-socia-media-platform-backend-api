<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Notifications\LikeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendLikeNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user , public Post $post)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        if($this->user->id === $this->post->user_id)return ;
        
        $this->post->user->notify(
            new LikeNotification($this->user , $this->post)
        );
    }
}
