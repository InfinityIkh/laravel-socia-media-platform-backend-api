<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ProcessMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessUserImagesJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user ,public string $path)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessMediaService $processMediaService): void
    {
        //
        $oldImage = $this->user->path;

        $outputImage = $processMediaService->processImage($this->path);

        $this->user->update([
            'path' => $outputImage
        ]);

        if($outputImage){
            Storage::disk('public')->delete($oldImage);
        }
    }
}
