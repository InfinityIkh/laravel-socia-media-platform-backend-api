<?php

namespace App\Listeners;

use App\Events\UserFollowEvent;
use App\Jobs\SendFollowNotificationJob;
use App\Notifications\FollowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FollowingUserListener
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
    public function handle(UserFollowEvent $event): void
    {
        //
        SendFollowNotificationJob::dispatch($event->follower ,$event->followed);
    }
}
