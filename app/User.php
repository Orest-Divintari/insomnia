<?php

namespace App;

use App\Events\Profile\NewPostWasAddedToProfile;
use App\Traits\Followable;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{

    use Notifiable, Followable;

    /**
     * Set the maximum length for a username
     */
    const NAME_LENGTH = 10;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_path', 'short_name'];

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
    public function getMessageCountAttribute()
    {
        return $this->profilePosts()->count();
    }

    /**
     * Get the number of likes the user has received
     *
     * @return int
     */
    public function getLikeScoreAttribute()
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
        return $this->hasMany(ProfilePost::class, 'profile_owner_id');
    }

    /**
     * Add a new post to given profile
     *
     * @param array $post
     * @param User $profileOwner
     * @return ProfilePost
     */
    public function postToProfile($post, $profileOwner)
    {
        $poster = auth()->user();
        $post = ProfilePost::create([
            'body' => $post,
            'profile_owner_id' => $profileOwner->id,
            'user_id' => $poster->id,
        ]);

        event(new NewPostWasAddedToProfile($post, $poster, $profileOwner));

        return $post;

    }

    /**
     * Get all activities for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the number of posts on user's profile
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithMessageCount($query)
    {
        return $query->addSelect([
            'message_count' => ProfilePost::select(DB::raw('count(*)'))
                ->whereColumn('profile_owner_id', 'users.id'),
        ]);
    }

    /**
     * Get the total number of likes for user's posts
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithLikeScore($query)
    {
        return $query->addSelect([
            'like_score' => Reply::select(DB::raw('count(*)'))
                ->whereColumn('replies.user_id', 'users.id')
                ->join('likes', 'replies.id', '=', 'likes.reply_id'),
        ]);
    }

    /**
     * Get the user's profile information
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithProfileInfo($query)
    {
        return $query
            ->withMessageCount()
            ->withLikeScore();
    }

    /**
     * Eager load the users that follows and the number of users
     *
     * @param [type] $query
     * @return void
     */
    public function scopeWithFollows($query)
    {
        return $query->with('follows')->withCount('follows');
    }

    /**
     * A user may have many conversations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conversations()
    {
        return $this->belongsToMany(
            Conversation::class,
            'conversation_participants',
            'user_id',
            'conversation_id'
        )->wherePivot('hid', false)
            ->wherePivot('left', false);
    }

    /**
     * Mark a conversation as read
     *
     * @param Conversation $conversation
     * @return void
     */
    public function readConversation(Conversation $conversation)
    {
        $conversation->reads()
            ->where('user_id', $this->id)
            ->update(['read_at' => Carbon::now()]);
    }

    /**
     * Mark a conversation as unread
     *
     * @param Conversation $conversation
     * @return void
     */
    public function unreadConversation(Conversation $conversation)
    {
        $conversation->reads()
            ->where('user_id', auth()->id())
            ->update(['read_at' => null]);
    }

    /**
     * Get the unread conversations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function unreadConversations()
    {
        return $this->conversations()
            ->whereHas('reads', function ($query) {
                $query->where('reads.user_id', $this->id)
                    ->where(function ($query) {
                        $query
                            ->whereColumn('reads.read_at', '<', 'conversations.updated_at')
                            ->orWhereNull('reads.read_at');
                    });
            });
    }
}