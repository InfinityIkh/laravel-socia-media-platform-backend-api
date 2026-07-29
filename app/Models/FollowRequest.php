<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowRequest extends Model
{
    //
    protected $table = 'follow_requests';
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status'
    ];

    public function sender():BelongsTo{
        return $this->belongsTo(User::class,'sender_id');
    }

    public function receiver():BelongsTo{
        return $this->belongsTo(User::class,'receiver_id');
    }
}
