<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ProcessMediaService{

    public function processMessagesFiles(string $type ,string $path): string
    {
        //
        return match($type){
            'image' => $this->processMessageImage($path),
            'document' => $this->processMessageDocument($path),
            'video' => $this->processMessageVideo($path)
        };
    }

    public function processMessageVideo(string $path): string
    {
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $processedPath = "messagesMedia/videos/processed/{$fileName}.mp4";

        $format = new X264('aac', 'libx264');
        $format->setKiloBitrate(2500);

        FFMpeg::fromDisk('public')
            ->open($path)
            ->export()
            ->toDisk('public')
            ->inFormat($format)
            ->resize(1280, 720, 'fit')
            ->save($processedPath);

        return $processedPath;
    }

    public function processMessageImage(string $path): string
    {
        //
        $manager = new ImageManager(
            new Driver()
        );
        $fullPath = Storage::disk('public')->path($path);
        $image = $manager->read($fullPath);
        $fileName = pathinfo($path ,PATHINFO_FILENAME);
        $outputImage = 'messagesMedia/images/processed/'.$fileName.'.webp';
        $image->scaleDown(width:600)
              ->toWebp(quality: 80)
              ->save(
                Storage::disk('public')->path($outputImage)
            );
        return $outputImage;
    }

    public function processMessageDocument(string $path): string
    {
        //
        $fileName = basename($path);
        $outputPath = 'messagesMedia/documents/processed/'.$fileName;

        Storage::disk('public')->copy(
            $path,
            $outputPath
        );
        return $outputPath;
    }
    
    public function processImage(string $path): string
    {
        //
        $manager = new ImageManager(
            new Driver()
        );

        $valid = explode('/',$path)[1];
        $fullPath = Storage::disk('public')->path($path);
        $image = $manager->read($fullPath);
        $fileName = pathinfo($path ,PATHINFO_FILENAME);
        if($valid === 'posts'){
            $outputImage = 'posts/processed/'.$fileName.'.webp';
        }else{
            $outputImage = 'profiles/processed/'.$fileName.'.webp';
        }
        $image->scaleDown(width:600)
              ->toWebp(quality: 80)
              ->save(
                Storage::disk('public')->path($outputImage)
              );
        return $outputImage;
    }
    public function processVideo(string $path , string $type): string
    {
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $processedPath = 'stories/'.$type.'/processed/'.$fileName.'.mp4';

        $format = new X264('aac', 'libx264');
        $format->setKiloBitrate(2500);

        FFMpeg::fromDisk('public')
            ->open($path)
            ->export()
            ->toDisk('public')
            ->inFormat($format)
            ->resize(1280, 720, 'fit')
            ->save($processedPath);

        return $processedPath;
    }

    public function processStoryImage(string $path , string $type){
        //
        $manager = new ImageManager(
            new Driver()
        );

        $fullPath = Storage::disk('public')->path($path);
        $image = $manager->read($fullPath);
        $fileName = pathinfo($path ,PATHINFO_FILENAME);
        $outputImage = 'stories/'.$type.'/processed/'.$fileName.'.webp';
        $image->scaleDown(width:600)
              ->toWebp(quality: 80)
              ->save(
                Storage::disk('public')->path($outputImage)
              );
        return $outputImage;
    }
}