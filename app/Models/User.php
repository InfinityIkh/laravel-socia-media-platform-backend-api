<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'path',
        'is_private'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function scopeVisible(Builder $query ,User $user){
        $blockedUsers = $user->blockedUsers()->pluck('users.id');
        $blockedMe = $user->blockedByUsers()->pluck('users.id');
        $usersIds = $blockedUsers->merge($blockedMe)->unique();
        return $query->whereNotIn('id',$usersIds);
    }

    public function posts():HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function replys(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class,'likes')->withTimestamps();
    }

    public function repostedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class,'reposts')->withTimestamps();
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'saves')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'users_followers_following','following_id','follower_id');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'users_followers_following','follower_id','following_id');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class,'reportable');
    }

    public function blockedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'blocked_users','blocker_id','blocked_id');
    }

    public function blockedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'blocked_users','blocked_id','blocker_id');
    }

    public function followRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'follow_requests','receiver_id','sender_id')
                    ->wherePivot('status', 'pending')
                    ->withPivot('id')
                    ->withTimestamps();
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function viewedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'posts_views', 'user_id', 'post_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Messages::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class ,'user_conversations' ,'user_id' ,'conversation_id');
    }
}
