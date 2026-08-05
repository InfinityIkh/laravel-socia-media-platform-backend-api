<?php

namespace App\Listeners;

use App\Events\UserLikedEvent;
use App\Jobs\SendLikeNotificationJob;
use App\Notifications\LikeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LikePostListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLikedEvent $event): void
    {
        //
        SendLikeNotificationJob::dispatch($event->user ,$event->post);
    }
}
