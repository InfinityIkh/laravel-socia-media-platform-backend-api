<?php

namespace App\Jobs;

use App\Services\StoryServices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteExpiredStoriesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(StoryServices $storyServices): void
    {
        //
        $storyServices->deleteExpiredStories();
    }
}
