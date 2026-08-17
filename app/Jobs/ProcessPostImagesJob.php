<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\ProcessMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessPostImagesJob implements ShouldQueue
{
    use Queueable;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post ,public string $path)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessMediaService $processMediaService): void
    {
        //
        $oldImages = $this->post->images()->get();

        $outputImage = $processMediaService->processImage($this->path);

        $this->post->images()->create([
            'image_path' => $outputImage
        ]);

        foreach($oldImages as $image){
            Storage::disk('public')->delete($image->image_path);
        }
        $oldImages->each->delete();
    }
}
