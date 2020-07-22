<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    const NAME_LENGTH = 10;
    protected $appends = ['avatar_path', 'short_name'];
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * Determines the path to user's avatar
     *
     * @param string $avatar
     * @return string
     */
    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ?: '/avatars/users/user_logo.png');
    }

    /**
     * Shorten the length of the name
     *
     * @return string
     */
    public function getShortNameAttribute()
    {
        return Str::limit($this->name, static::NAME_LENGTH, '');
    }

    /**
     * Mark the given thread as read by the authenticated user
     *
     * @param Thread $thread
     * @return void
     */
    public function read($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    /**
     * Generate a cache key to be used when a user reads a thread
     *
     * @param Thread $thread
     * @return string
     */
    public function visitedThreadCacheKey($thread)
    {
        return sprintf("users.%s.visits.%s", $this->id, $thread->id);
    }

    /**
     * Fetch the posts that were liked by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * A user has replies
     *
     * @return void
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get the subscriptions associated with the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Fetch the subscription for a specific thread
     *
     * @param  integer $threadId
     * @return
     */
    public function subscription($threadId)
    {
        return ThreadSubscription::Of($threadId, $this->id);
    }

    /**
     * Get the number of replies the user has made
     *
     * @return int
     */
    public function getMessagesCountAttribute()
    {
        return $this->replies()->count();
    }

    /**
     * Get the number of likes the user has received
     *
     * @return int
     */
    public function getLikesScoreAttribute()
    {
        return Reply::where('replies.user_id', $this->id)
            ->join('likes', 'replies.id', '=', 'likes.reply_id')
            ->count();
    }

    /**
     * Format the date the user joined the forum
     *
     * @return void
     */
    public function getJoinDateAttribute()
    {
        return $this->created_at->toFormattedDateString();
    }

    /**
     * Get the profile posts of the user
     *
     * @return void
     */
    public function profilePosts()
    {
        return $this->hasMany(ProfilePost::class, 'profile_user_id');
    }
}