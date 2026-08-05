<?php

namespace App\Listeners;

use App\Events\UserCommentedEvent;
use App\Jobs\SendCommentNotificationJob;
use App\Notifications\CommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentPostListener
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
    public function handle(UserCommentedEvent $event): void
    {
        //
        SendCommentNotificationJob::dispatch();
    }
}
