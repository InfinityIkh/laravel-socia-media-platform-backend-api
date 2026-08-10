<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessMediaService{

    public function processImage(string $path){
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
}