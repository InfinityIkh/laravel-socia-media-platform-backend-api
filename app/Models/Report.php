<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $guarded = [];

    public function repostable():MorphTo
    {
        return $this->morphTo();
    }
}
