<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\Guard;

class PostLike extends Model
{
    //
    protected $table = 'likes';
    protected $guarded = [];
}
