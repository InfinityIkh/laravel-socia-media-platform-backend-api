<?php

namespace App\Jobs;

use App\Models\Story;
use App\Services\ProcessMediaService;
use App\Services\StoryServices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessvideoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Story $story ,public string $path)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessMediaService $processMediaService): void
    {
        //
        $path = $processMediaService->processVideo($this->path ,$this->story->media_type);
        $this->story->update([
            'media_path' => $path
        ]);
    }
}
