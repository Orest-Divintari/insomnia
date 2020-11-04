<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Likeable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{
    use Filterable, Likeable, FormatsDate, RecordsActivity, Searchable;

    /**
     * Number of thread replies per page
     *
     * @var int
     */
    const REPLIES_PER_PAGE = 10;

    /**
     * Number of post profile comments per page
     *
     * @var int
     */
    const COMMENTS_PER_PAGE = 3;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_updated',
        'date_created',
        'type',
    ];

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */

    protected $guarded = [];

    /**
     * Relationships to always eager-load
     *
     * @var array
     */
    protected $with = ['poster'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['repliable'];

    /**
     * Boot the Model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($reply) {

            if ($reply->repliable_type == 'App\Thread') {
                $reply->repliable->decrement('replies_count');
            }

            $reply->likes->each->delete();

            $reply->activities->each->delete();
        });
    }

    /**
     * Touch the Thread relationship
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'repliable_id');
    }

    /**
     * A reply belongs to a repliable model
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function repliable()
    {
        return $this->morphTo();
    }

    /**
     * A thread reply belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');

    }

    /**
     * A comment belongs to a profile post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profilePost()
    {
        return $this->belongsTo(ProfilePost::class, 'repliable_id');
    }

    /**
     * Sanitize the body from malicious context
     *
     * @param string $body
     * @return string
     */
    public function getBodyAttribute($body)
    {
        return Purify::clean($body);
    }

    /**
     * Get the number of the page a specific reply belongs to
     *
     * @param Thread $thread
     * @return void
     */
    public function getPageNumberAttribute()
    {
        $numberOfRepliesBefore = $this->repliable
            ->replies()
            ->where('id', '<=', $this->id)
            ->count();

        return (int) ceil($numberOfRepliesBefore / Reply::REPLIES_PER_PAGE);
    }

    /**
     * Get paginated replies with likes for a specific thread
     *
     * @param  $thread
     * @param App\Filters\ReplyFilters $filters
     * @return Model
     */
    public static function forThread($thread, $filters)
    {
        $replies = static::where('repliable_id', $thread->id)
            ->where('repliable_type', Thread::class)
            ->withLikes()
            ->filter($filters)
            ->paginate(static::REPLIES_PER_PAGE);

        $replies->each(function ($reply) {
            $reply->append('is_liked');
        });

        return $replies;
    }

    /**
     * Get paginated comments with likes for a specific profile post
     *
     * @param  $thread
     * @param App\Filters\ReplyFilters $filters
     * @return Model
     */
    public static function forProfilePost($post)
    {
        $comments = static::where('repliable_id', $post->id)
            ->where('repliable_type', ProfilePost::class)
            ->withLikes()
            ->latest()
            ->paginate(static::COMMENTS_PER_PAGE);

        $comments->each(function ($comment) {
            $comment->append('is_liked');
        });

        return $comments;
    }

    /**
     * Get the activities of the reply
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get the indexable data array for the model
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->withoutRelations()->toArray();
        if ($this->repliable_type == 'App\ProfilePost') {
            $array['profile_owner_id'] = $this->repliable->profile_owner_id;
        }
        return $array;
    }

    /**
     * Get the type of the model
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        if ($this->repliable_type == 'App\Thread') {
            return 'thread-reply';
        } elseif ($this->repliable_type == 'App\ProfilePost') {
            return 'profile-post-comment';
        }
    }

    /**
     * The first thread reply consists the body of the thread
     * therefore it should not be searchable
     *
     * @return boolean
     */
    public function shouldBeSearchable()
    {
        if ($this->repliable_type == 'App\Thread') {
            return $this->position > 1;
        }
        return true;
    }

    /**
     * Get the informatiomn that is required to display a thread reply or a comment
     * as a search result with algolia
     *
     * @param Builer $query
     * @return Builer
     */
    public function scopeWithSearchInfo($query)
    {
        return $query->with(['repliable' => function (MorphTo $morphTo) {
            $morphTo->morphWith([
                Thread::class => ['poster', 'category'],
                ProfilePost::class => ['profileOwner'],
            ]);
        }]);
    }

    /**
     * Get only the thread replies
     * and exclude the replies that consist the body of a thread
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyReplies($query)
    {
        return $query->where([
            ['repliable_type', '=', 'App\Thread'],
            ['position', '>', '1'],
        ]);
    }

    /**
     * Get only the profile post comments
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyComments($query)
    {
        return $query->where('repliable_type', 'App\ProfilePost');
    }

}