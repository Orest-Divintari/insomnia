<?php

namespace App\Models;

use App\Events\Profile\NewPostWasAddedToProfile;
use App\Facades\Avatar;
use App\Filters\ExcludeIgnoredFilter;
use App\Traits\Followable;
use App\Traits\HandlesPrivacy;
use App\Traits\Ignorable;
use App\User\Details;
use App\User\Preferences;
use Carbon\Carbon;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{

    use Ignorable, Notifiable, Followable, Searchable, QueryDsl, HandlesPrivacy, HasRoles, HasFactory;

    /**
     * Set the maximum length for a username
     */
    const NAME_LENGTH = 10;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['short_name', 'permissions', 'verified'];

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
        'ignored_by_visitor' => 'boolean',
        'default_avatar' => 'boolean',
        'details' => 'json',
        'privacy' => 'json',
        'preferences' => 'json',
    ];

    protected $dates = ['notifications_viewed_at'];

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
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $attributes = $this->toArray();
        // ensure the consistency of the date format
        // for the cases where $timestamps is set to false
        // when $timestamps is set to false, prior to updating the created_at and updated_at
        // the format of the dates changes,
        // and as a result it conflcits with the format that is in the index
        $attributes['created_at'] = Carbon::parse($attributes['created_at'])->toJson();
        $attributes['updated_at'] = Carbon::parse($attributes['updated_at'])->toJson();
        return $attributes;
    }

    /**
     * Hash the password before persisting
     *
     * @param String $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
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
        asset('storage/' . $avatar) :
        Avatar::generate($this->name);
    }

    public function getGravatarPathAttribute($gravatar)
    {
        return $gravatar ?? 'http://www.gravatar.com/avatar?s=400';
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
     * User may like a post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'liker_id');
    }

    /**
     * Get the likes the user has received
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedLikes()
    {
        return $this->hasMany(Like::class, 'likee_id');
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
     * @param User $authUser
     * @return Builder
     */
    public function scopeWithProfileInfo($query, $authUser)
    {
        return $query->withCount('profilePosts')
            ->withCount('receivedLikes')
            ->withFollowedByVisitor()
            ->withIgnoredByVisitor($authUser);
    }

    public function ignoredUserIds()
    {
        return Ignoration::where('user_id', $this->id)
            ->where('ignorable_type', User::class)
            ->pluck('ignorable_id');
    }

    /**
     *  User has ignored items
     *
     * @return void
     */
    public function ignorings()
    {
        return $this->hasMany(Ignoration::class);
    }

    /**
     * Eager load the users that follows and the number of users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithFollowings($query)
    {
        return $query
            ->with('followings')
            ->withCount('followings');
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
        )->wherePivot('hidden', false)
            ->wherePivot('left', false);
    }

    /**
     * Add the column which determines whether the user is a conversation admin
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithConversationAdmin($query, $conversation)
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
        $unreadConversations = $this->conversations()
            ->whereHas('reads', function ($query) {
                $query->where('reads.user_id', $this->id)
                    ->where(function ($query) {
                        $query
                            ->whereColumn('reads.read_at', '<', 'conversations.updated_at')
                            ->orWhereNull('reads.read_at');
                    });
            });

        return app(ExcludeIgnoredFilter::class)
            ->apply($unreadConversations, $this);
    }

    /**
     * Get the number of unread conversations
     *
     * @return integer
     */
    public function getUnreadConversationsCountAttribute()
    {
        return $this->unreadConversations()->count();
    }

    /**
     * Determine if the user is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
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
                'App\Models\Thread',
                'App\Models\Reply',
                'App\Models\ProfilePost',
            ])->where('type', 'like', 'created%')
            ->latest('created_at')
            ->first();
    }

    /**
     * Mark the notifications as viewed
     *
     * @return void
     */
    public function viewNotifications()
    {
        $this->update(['notifications_viewed_at' => Carbon::now()]);
    }

    /**
     * Determine whether the user has viewed the notifications
     *
     * @return boolean
     */
    public function notificationsViewed()
    {
        if (!$latestNotification = $this->unreadNotifications()->latest()->first()) {
            return true;
        }
        return $this->notifications_viewed_at > $latestNotification->created_at;
    }

    /**
     * Get the number of unviewed notifications
     *
     * @return integer
     */
    public function getUnviewedNotificationsCountAttribute()
    {
        if ($this->notifications_viewed_at) {

            return $this->unreadNotifications()
                ->where('created_at', '>', $this->notifications_viewed_at)
                ->count();
        }

        return $this->unreadNotifications()->count();
    }

    /**
     * Get the user's details
     *
     * @return App\User\Details
     */
    public function details()
    {
        return new Details($this->details, $this);
    }

    /**
     * Find users by name
     *
     * @param Builder $query
     * @param string|string[] $names
     * @return Builder
     */
    public function scopefindByName($query, $names)
    {
        if (is_array($names)) {
            return $query->whereIn('name', $names);
        }

        return $query->where('name', $names);
    }

    /**
     * Get the date of birth
     *
     * @return string|null
     */
    public function getDateOfBirthAttribute()
    {
        if (!$birthDate = $this->details()->birth_date) {
            return;
        }

        if ($this->allows('show_birth_date')) {

            $dateOfBirth = Carbon::make($this->details()->birth_date);

            if ($this->allows('show_birth_year')) {
                return $dateOfBirth->format('M d, Y') . " ( Age: {$dateOfBirth->age} )";
            }
            return $dateOfBirth->format('M d');
        }
    }

    /**
     * Append the permissions of the user
     *
     * @return array
     */
    public function getPermissionsAttribute()
    {
        $user = optional(auth()->user());

        return [
            'post_on_profile' => $user->can('post_on_profile', $this),
            'start_conversation' => $user->can('view_start_conversation_button', [Conversation::class, $this]),
            'view_identities' => $user->can('view_identities', $this),
            'view_current_activity' => $user->can('view_current', [Activity::class, $this]),
            'ignore' => $user->can('view_ignore_button', $this),
            'follow' => $user->can('view_follow_button', $this),
            'like' => $user->can('view_button', Like::class),
        ];
    }

    /**
     * Get an instance of the user preferences settings
     *
     * @return Preferences
     */
    public function preferences()
    {
        return new Preferences($this->preferences, $this);
    }

    /**
     * Fetch the users that are ignored by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ignoredUsers()
    {
        return $this->hasManyThrough(
            User::class,
            Ignoration::class,
            'user_id',
            'id',
            'id',
            'ignorable_id'
        )->where('ignorable_type', User::class);
    }

    /**
     * Fetch the threads that are ignored by the user
     *
     * @return Builder
     */
    public function ignoredThreads()
    {
        return Thread::whereIn(
            'id',
            Ignoration::select('ignorable_id')
                ->where('user_id', $this->id)
                ->where('ignorable_type', Thread::class)
        );
    }

    /**
     * Mark an ignorable model as ignored
     *
     * @param mixed $ignorable
     * @return Ignoration
     */
    public function ignore($ignorable)
    {
        return $ignorable
            ->ignorations()
            ->create(['user_id' => $this->id]);
    }

    /**
     * Mark an ignorabel model as unignored
     *
     * @param mixed $ignorable
     * @return void
     */
    public function unignore($ignorable)
    {
        $ignorable->ignorations()
            ->where('user_id', $this->id)
            ->delete();
    }

    /**
     * Fetch notifications since last week
     *
     * @return Builder
     */
    public function notificationsSinceLastWeek()
    {
        return $this->notifications()->where('created_at', '>=', now()->subWeek());
    }

    /**
     * Determine whether the user has not verified the email
     *
     * @return boolean
     */
    public function hasNotVerifiedEmail()
    {
        return !$this->hasVerifiedEmail();
    }

    /**
     * Get the attribute that determines whether the user has verified the account
     *
     * @return void
     */
    public function getVerifiedAttribute()
    {
        return $this->hasVerifiedEmail();
    }

    /**
     * It filters out the given user
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeExcept($query, $user)
    {
        return $query->when(isset($user), function ($query) use ($user) {
            $query->where('users.id', '!=', $user->id);
        });
    }

    /**
     * It filters out the unverified users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}