<?php

namespace App;

use App\Events\Subscription\NewReplyWasPostedToThread;
use App\Search\Threads;
use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Lockable;
use App\Traits\Readable;
use App\Traits\RecordsActivity;
use App\Traits\Sluggable;
use App\Traits\Subscribable;
use App\User;
use Carbon\Carbon;
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
    Sluggable,
    Lockable,
        Readable;
    /**
     * Number of visible threads per page
     *
     * @var int
     */
    const PER_PAGE = 5;

    /**
     * Number of thread replies per page;
     *
     * @var int
     */
    const REPLIES_PER_PAGE = 10;

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
        'type',
        'last_pages',
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'locked' => 'boolean',
        'pinned' => 'boolean',
        'has_been_updated' => 'boolean',
        'replies_count' => 'int',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($thread) {
            $thread->createReplyForThreadBody();
            if (auth()->check()) {
                $thread->subscribe(auth()->id());
            }
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
            $thread->subscriptions->each->delete();
        });
    }

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
        return '/ajax/threads/' . $this->slug;
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
     * Determine whether the thread has replies
     *
     * @return boolean
     */
    public function hasReplies()
    {
        return $this->replies_count > 0;
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
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
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
     * Add a reply to a thread
     *
     * @param array $reply
     * @return Reply $reply;
     */
    public function addReply($reply, $poster = null)
    {
        $poster = $poster ?? auth()->user();

        $reply = $this->replies()->create([
            'body' => $reply,
            'user_id' => $poster->id,
        ]);

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
     * Create a reply which is the body of the thread
     * Do not sync this reply with algolia
     *
     * @return void
     */
    public function createReplyForThreadBody()
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
        return $query->with(['poster', 'category', 'tags']);
    }

    /**
     * Determine if the activity for this model should be recorded
     *
     * @return boolean
     */
    public function shouldBeRecordable()
    {
        return true;
    }

    /**
     * A thread can be read by many users
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function reads()
    {
        return $this->morphMany(Read::class, 'readable');
    }

    /**
     * Mark thread as pinned
     *
     * @param boolean $pinned
     * @return void
     */
    public function pin($pinned = true)
    {
        $this->pinned = $pinned;
        $this->save();
    }

    /**
     * Mark thread as unpinned
     *
     * @return void
     */
    public function unpin()
    {
        $this->pin($pinned = false);
    }

    /**
     * A thread has many tags
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'thread_tags');
    }

    /**
     * Correlate tags to a thread
     *
     * @param array $tags
     */
    public function addTags(array $tags)
    {
        $tagIds = Tag::whereIn('name', $tags)->pluck('id');
        $this->tags()->attach($tagIds);
    }

    // return $query->addSelect(
    //     ['has_been_updated' => Read::select(DB::raw(
    //         'CASE
    //             WHEN reads.read_at >= threads.updated_at then 0
    //             ELSE 1
    //         END'))->whereColumn('reads.readable_id', 'threads.id')
    //             ->where('reads.readable_type', Thread::class)
    //             ->where('user_id', '=', auth()->id()),
    //     ]);

    // return $query->select('threads.*', 'reads.read_at')
    //     ->leftJoin('reads', function ($join) {
    //         $join->on('reads.readable_id', '=', 'threads.id')
    //             ->where('reads.readable_type', '=', Thread::class)
    //             ->where('reads.user_id', auth()->id());
    //     })->selectRaw(
    //     'CASE
    //         WHEN read_at >= threads.updated_at THEN 0
    //         WHEN read_at IS NULL THEN 1
    //         ELSE 1
    //     END as has_been_updated'
    // );

    // public function getHasBeenUpdatedAttribute($value)
    // {
    //     if (is_null($value) || $value == "1") {
    //         return true;
    //     } else if ($value == "0") {
    //         return false;
    //     }
    //     return $value;
    // }

    /**
     * Get the pinned threads
     *
     * @param Builder $query
     * @return void
     */
    public function scopePinned($query)
    {
        return $query->where('pinned', true);
    }

    /*
     * Filter threads for the given category
     *
     * @param Builder $query
     * @param Categorry $category
     * @return Builder
     */
    public function scopeForCategory($query, $category)
    {
        if (isset($category->id)) {
            return $query->where('category_id', $category->id);
        }
        return $query;
    }

    /**
     * Get the last pages of replies for the thread
     *
     * @return array
     */
    public function getLastPagesAttribute()
    {
        $threadBodyReply = 1;
        $numberOfLastPages = 3;
        $pages = (int) ceil(($this->replies_count + $threadBodyReply) / static::REPLIES_PER_PAGE);
        $link = "/threads/{$this->slug}?page=";
        $lastPages = [];
        if ($pages < 2) {
            return [];
        }
        if ($pages == 2) {
            return [2 => $this->linkToPage(2)];
        }
        if ($pages == 3) {
            return [2 => $this->linkToPage(2), 3 => $this->linkToPage(3)];
        }
        for ($pageCount = $pages; $pageCount > $pages - $numberOfLastPages; $pageCount--) {
            $lastPages[$pageCount] = $this->linkToPage($pageCount);
        }
        ksort($lastPages);
        return $lastPages;
    }

    /**
     * Get the link to the given page
     *
     * @param int $page
     * @return string
     */
    public function linkToPage($page)
    {
        return "/threads/{$this->slug}?page={$page}";
    }

    /**
     * Fetch the threads that were read recently by the givne user
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeRecentlyViewedBy($query, $user)
    {
        $days = 30;
        $daysAgo = Carbon::now()->subDays($days)->startOfDay();

        return $query->select()->whereHas('reads', function ($query) use (
            $user,
            $daysAgo
        ) {
            $query->where('read_at', '>=', $daysAgo)
                ->where('user_id', $user->id);
        });
    }

    /**
     * Get the date that the authenticated user read a thread
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithReadAt($query)
    {
        return $query->addSelect(['read_at' => Read::select('read_at')
                ->whereColumn('readable_id', 'threads.id')
                ->where('readable_type', Thread::class)
                ->when(auth()->check(), function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
        ]);
    }

    /**
     * Format the date that the thread was read
     *
     * @param string $value
     * @return string
     */
    public function getReadAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

}