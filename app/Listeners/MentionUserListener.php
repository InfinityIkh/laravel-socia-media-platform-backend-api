<?php

namespace App\Listeners;

use App\Events\UserMentionEvent;
use App\Jobs\SendMentionNotificationJob;
use App\Notifications\MentionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MentionUserListener
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
    public function handle(UserMentionEvent $event): void
    {
        //
        SendMentionNotificationJob::dispatch();
    }
}
