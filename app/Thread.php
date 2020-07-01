<?php

namespace App;

use App\Events\Subscription\NewReplyWasPostedToThread;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Thread extends Model
{

    const PER_PAGE = 1;
    const TITLE_LENGTH = 25;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'short_title',
        'date_created',
        'date_updated',
        'has_been_updated',
    ];

    /**
     * Relationships to always eager-load
     *
     * @var array
     */
    protected $with = ['recentReply', 'poster'];

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'body', 'user_id', 'category_id'];

    /**
     * Get the route key name
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the string api path for the thread
     *
     * @return string
     */
    public function api_path()
    {
        return '/api/threads/' . $this->slug;
    }

    /**
     * Get the replies associated with the thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function replies()
    {
        return $this->morphMany(Reply::class, 'repliable');
    }

    public function recentReply()
    {
        return $this->morphOne(Reply::class, 'repliable')
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * Get the user that created the thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread belongs to a category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Assert the slug is unique
     *
     * @param string $slug
     * @return void
     */
    public function setSlugAttribute($slug)
    {
        if (Thread::where('slug', $slug)->exists()) {
            $slug = $this->createUnique($slug);
        }
        $this->attributes['slug'] = $slug;

    }

    /**
     * Create a unique slug
     *
     * @param string $slug
     * @return string $slug
     */
    protected function createUnique($slug)
    {
        $counter = 2;
        $originalSlug = $slug;
        while (Thread::whereSlug($slug)->exists()) {

            $slug = "{$originalSlug}.{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Shorten the length of the title
     *
     * @return string
     */
    public function getShortTitleAttribute()
    {
        return Str::limit($this->title, static::TITLE_LENGTH, '');
    }

    /**
     * Transform the date it was updated to human readable datetime
     *
     * @return string
     */
    public function getDateUpdatedAttribute()
    {
        return $this->updated_at->calendar();
    }

    /**
     * Transform the date it was created to human readable datetime
     *
     * @return string
     */
    public function getDateCreatedAttribute()
    {
        return $this->updated_at->calendar();
    }

    /**
     * Determine whether the thread has been updated since last read
     *
     * @return boolean
     */
    public function getHasBeenUpdatedAttribute()
    {
        if (!auth()->check()) {
            return true;
        }
        $key = auth()->user()->visitedThreadCacheKey($this);
        return $this->updated_at > cache($key);
    }

    /**
     * Add a reply to a thread
     *
     * @param array $reply
     * @return Reply $reply;
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);
        $this->increment('replies_count');
        event(new NewReplyWasPostedToThread($this, $reply));
        return $reply;
    }

    /**
     * A thread has subscribers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'thread_subscriptions');
    }

    /**
     * Subscribe a user to the current thread
     *
     * @param int|null $userId
     * @return void
     */
    public function subscribe($userId = null)
    {
        $this->subscribers()->attach([
            'user_id' => $userId ?? auth()->id(),
        ])->withTimestamps();
    }

    /**
     * Unsubscribe a user from the current thread
     *
     * @param int|null $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscribers()->detach([
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Determine whether the authenicated user has subscribed to current thread
     *
     * @return boolean
     */
    public function getSubscribedByAuthUserAttribute()
    {
        return $this->subscribers()->where([
            'user_id' => auth()->id(),
        ])->exists();

    }

    /**
     * Determine whether a user is subscribed to current thread
     *
     * @param int $userId
     * @return boolean
     */
    public function isSubscribedBy($userId)
    {
        return $this->subscribers()->where([
            'user_id' => $userId,
        ])->exists();
    }

}