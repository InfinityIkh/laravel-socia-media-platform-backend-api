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
        $manager = new ImageManager(
            new Driver()
        );
        return match($type){
            'image' => $this->processMessageImage($manager ,$path),
            'document' => $this->processMessageDocument($path),
            'video' => $this->processMessageVideo($path)
        };
    }

    public function processMessageVideo(string $path): string
    {
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        $processedPath = "messagesMedia/video/processed/{$fileName}.mp4";

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

    public function processMessageImage(ImageManager $manager ,string $path): string
    {
        //
        $fullPath = Storage::disk('public')->path($path);
        $image = $manager->read($fullPath);
        $fileName = pathinfo($path ,PATHINFO_FILENAME);
        $outputImage = 'images/messagesMedia/image/processed/'.$fileName.'.webp';
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
        $outputPath = 'messagesMedia/document/processed/'.$fileName;

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
            $outputImage = 'images/posts/processed/'.$fileName.'.webp';
        }else{
            $outputImage = 'images/profiles/processed/'.$fileName.'.webp';
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
        $processedPath = 'images/'.$type.'/processed/'.$fileName.'.mp4';

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
}