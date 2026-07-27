<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_id',
        'views_count'
    ];

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

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
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
