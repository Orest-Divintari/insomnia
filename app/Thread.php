<?php

namespace App;

use App\Events\Subscription\NewReplyWasPostedToThread;
use App\Search\Threads;
use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\RecordsActivity;
use App\Traits\Sluggable;
use App\Traits\Subscribable;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Thread extends Model
{

    use Filterable,
    FormatsDate,
    Subscribable,
    RecordsActivity,
    Searchable,
        Sluggable;

    /**
     * Number of visible threads per page
     *
     * @var int
     */
    const PER_PAGE = 5;

    /**
     * Shortened length of the thread title
     * @var int
     */
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
        'type',
    ];

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'body',
        'user_id',
        'category_id',
        'replies_count',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($thread) {
            $thread->createReply();
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
            $thread->subscriptions->each->delete();
        });
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

    /**
     * Get the most recent reply of a thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recentReply()
    {
        return $this->belongsTo(Reply::class);
    }

    /**
     * Eager load the most recent reply for a thread
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeWithRecentReply(Builder $query)
    {
        return $query->addSelect([
            'recent_reply_id' => Reply::select('id')
                ->whereColumn('repliable_id', 'threads.id')
                ->where('repliable_type', 'App\Thread')
                ->latest('created_at')
                ->take(1),
        ])->with('recentReply.poster');

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
     * Shorten the length of the title
     *
     * @return string
     */
    public function getShortTitleAttribute()
    {
        return Str::limit($this->title, static::TITLE_LENGTH, '');
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

        $reply->update(['position' => $this->replies_count + 1]);

        event(new NewReplyWasPostedToThread($this, $reply));

        return $reply;
    }

    /**
     * User visits a thread
     *
     * @return boolean
     */
    public function recordVisit()
    {
        if (auth()->check()) {
            auth()->user()->read($this);
        }
        $this->increment('views');
    }

    /**
     *
     * Create a reply which is the body of the thread
     * Subscribe the creator to the thread
     * Do not sync this reply with algolia
     *
     * @return void
     */
    public function createReply()
    {
        $reply = new Reply();
        $reply->setTouchedRelations([]);
        $reply->body = $this->body;
        $reply->user_id = $this->user_id;
        $reply->updated_at = $this->updated_at;
        $reply->created_at = $this->created_at;
        $reply->repliable_id = $this->id;
        $reply->position = 1;
        $reply->repliable_type = 'App\Thread';
        $reply->save();

        if (auth()->check()) {
            $this->subscribe();
        }
    }

    /**
     * Get the activities of the thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'thread_title';
    }

    /**
     * Get the type of the model
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'thread';
    }

    /**
     * Get the information that is required to display a thread
     * as a search result with algolia
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithSearchInfo($query)
    {
        return $query->with(['poster', 'category']);
    }
}