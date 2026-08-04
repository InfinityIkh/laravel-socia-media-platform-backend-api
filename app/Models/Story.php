<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Story extends Model
{
    //
    protected $table = 'stories';
    protected $guarded = [];

    public function scopeVisible(Builder $query ,User $user){
        //
        $blockedUsers = $user->blockedUsers()->pluck('users.id');
        $blockedMe = $user->blockedByUsers()->pluck('users.id');
        $usersIds = $blockedUsers->merge($blockedMe)->unique();
        
        return $query->whereNot('user_id',$usersIds);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'story_views');
    }
}
