<?php

namespace App\Listeners;

use App\Events\UserRepostedEvent;
use App\Jobs\SendRepostNotificationJob;
use App\Notifications\RepostNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RepostPostListener
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
    public function handle(UserRepostedEvent $event): void
    {
        //
        SendRepostNotificationJob::dispatch($event->user ,$event->post);
    }
}
