<?php

namespace App\Listeners;

use App\Events\UserMentionEvent;
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
        if($event->user->id === $event->targetdUser->id)return;
        $event->user->notify(new MentionNotification($event->user , $event->post , $event->targetdUser));
    }
}
