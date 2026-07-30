<?php

namespace App\Listeners;

use App\Events\FollowRequestEvent;
use App\Notifications\FollowRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use function Laravel\Prompts\notify;

class FollowRequestListener
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
    public function handle(FollowRequestEvent $event): void
    {
        //
        $event->receiver->notify(
            new FollowRequestNotification($event->sender)
        );
    }
}
