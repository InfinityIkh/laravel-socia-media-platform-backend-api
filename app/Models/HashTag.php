<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HashTag extends Model
{
    //
    protected $table = 'hashtags';
    protected $guarded = [];

    public function posts():BelongsToMany 
    {
        return $this->belongsToMany(Post::class);
    }
}
