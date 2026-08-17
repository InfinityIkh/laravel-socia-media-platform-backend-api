<?php

namespace App\Jobs;

use App\Models\Story;
use App\Services\ProcessMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessStoryImageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Story $story ,public string $media_path)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessMediaService $processMediaService): void
    {
        //
        $path = $processMediaService->processStoryImage($this->media_path ,$this->story->media_type);
        $this->story->update([
            'media_path' => $path
        ]);
    }
}
