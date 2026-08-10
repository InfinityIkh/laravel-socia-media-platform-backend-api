<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImages extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'post_id',
        'image_path'
    ];

}
