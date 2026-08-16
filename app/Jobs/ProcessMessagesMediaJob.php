<?php

namespace App\Jobs;

use App\Models\Messages;
use App\Services\ProcessMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMessagesMediaJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Messages $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessMediaService $processMediaService): void
    {
        //
        if (!$this->message->media_path)return;

        $path = $processMediaService->processMessagesFiles($this->message->type ,$this->message->media_path);

        $this->message->update([
            'media_path' => $path,
        ]);
    }
}
