<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'body',
        'user_id',
    ];

    public function scopeVisible(Builder $query ,User $user){
        $blockedUsers = $user->blockedUsers()->pluck('users.id');
        $blockedMe = $user->blockedByUsers()->pluck('users.id');
        $usersIds = $blockedUsers->merge($blockedMe)->unique();
        return $query->whereNotIn('user_id',$usersIds);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'likes')->withTimestamps();
    }

    public function reposts(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'reposts')->withTimestamps();
    }

    public function saves(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'saves')->withTimestamps();
    }

    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'posts_views','post_id','user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImages::class);
    }

    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(HashTag::class,'hashtag_post','post_id','hashtag_id');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class,'reportable');
    }
}
