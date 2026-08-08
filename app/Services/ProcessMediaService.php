<?php

namespace App\Services;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessMediaService{

    public function processImage(string $path){
        //
        $manager = new ImageManager(
            new Driver()
        );
        $fullPath = Storage::disk('public')->path($path);
        $image = $manager->read($fullPath);
        $fileName = pathinfo($path ,PATHINFO_FILENAME);
        $outputImage = 'images/processed/'.$fileName.'.webp';
        $image->scaleDown(width:600)
              ->toWebp(quality: 80)
              ->save(
                Storage::disk('public')->path($outputImage)
              );
        return $outputImage;
    }
}