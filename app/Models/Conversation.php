<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    //
    use SoftDeletes;
    protected $guarded = [];

    public function messages(): HasMany
    {
        return $this->hasMany(Messages::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class ,'user_conversations' ,'conversation_id' ,'user_id');
    }
}
