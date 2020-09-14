<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\FormatsDate;
use App\Traits\Likeable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
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
     * Clean the body from malicious context
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
        $numberOfRepliesBefore = $this->thread
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

        // if ($this->repliable_type == 'App\Thread') {
        //     $type = 'thread_reply';
        // } elseif ($this->repliable_type == 'App\ProfilePost') {
        //     $type = 'profile_post_comment';
        // }

        // return [
        //     'body' => $this->body,
        //     'poster' => $this->poster->name,
        //     'created_at' => $this->created_at,
        //     'type' => $type,
        // ];

        return $this->withoutRelations()->toArray();

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

    public function shouldBeSearchable()
    {
        if ($this->repliable_type == 'App\Thread') {
            return $this->position > 1;
        }
        return true;
    }

}