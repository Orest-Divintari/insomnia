<?php

namespace App;

use App\Avatar\AvatarInterface;
use App\Events\Profile\NewPostWasAddedToProfile;
use App\Traits\Followable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail
{

    use Notifiable, Followable, Searchable;

    /**
     * Set the maximum length for a username
     */
    const NAME_LENGTH = 10;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatarPath', 'short_name'];

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

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
        'conversation_admin' => 'boolean',
        'followed_by_visitor' => 'boolean',
        'default_avatar' => 'boolean',
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
        return $avatar ?
        asset($avatar) :
        app(AvatarInterface::class)->generate($this->name);
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
     * Fetch the posts that were liked by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the total number of likes of user's posts
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithLikesCount($query)
    {
        return $query->addSelect([
            'likes_count' => Reply::select(DB::raw('count(*)'))
                ->whereColumn('replies.user_id', 'users.id')
                ->whereIn('repliable_type', [Thread::class, ProfilePost::class])
                ->join('likes', 'replies.id', '=', 'likes.reply_id'),
        ]);
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
     * Get the number of profile posts
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithMessagesCount($query)
    {
        return $query->withCount('profilePosts as messages_count');
    }

    /**
     * Get the user's profile information
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithProfileInfo($query)
    {
        $query->withMessagesCount()
            ->withLikesCount()
            ->withFollowedByVisitor();
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
     * Add the column which determines whether the user is a conversation admin
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsConversationAdmin($query, $conversation)
    {
        return $query->addSelect(
            ['conversation_admin' => ConversationParticipant::select('admin')
                    ->where(
                        'conversation_id',
                        $conversation->id
                    )->whereColumn(
                    'conversation_participants.user_id',
                    'users.id'
                ),
            ]
        );
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

    /**
     * Determine if the user is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return in_array($this->email, config('insomnia.administrators'));
    }

    /**
     * Get the user's thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Get the last post activity
     *
     * @return Activity
     */
    public function lastPostActivity()
    {
        return Activity::where('user_id', $this->id)
            ->whereIn('subject_type', [
                'App\Thread',
                'App\Reply',
                'App\ProfilePost',
            ])->latest('created_at')
            ->first();
    }
}