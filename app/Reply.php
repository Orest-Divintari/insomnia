<?php

namespace App;

use App\Events\Subscription\ReplyWasLiked;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{
    use Filterable;

    /**
     * Number of visible replies per page
     *
     * @var int
     */
    const PER_PAGE = 10;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_updated',
        'date_created',
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
    protected $touches = ['thread'];

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
     * A reply belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Transform the date that it was updated to readable format
     *
     * @return string
     */
    public function getDateUpdatedAttribute()
    {
        return $this->updated_at->calendar();
    }

    /**
     * Transform the date that it was created to readable format
     *
     * @return string
     */
    public function getDateCreatedAttribute()
    {
        return $this->created_at->calendar();
    }

    /**
     * Clean the body from malicious context
     *
     * @param string $body
     * @return string
     */
    public function getBodyAttriute($body)
    {
        return Purify::clean($body);
    }

    /**
     * A reply has likes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Like the current reply
     *
     * @param integer $userId
     * @return void
     */
    public function likedBy($userId = null)
    {
        $currentUserId = $userId ?: auth()->id();

        if (!$this->likes()->where('user_id', $currentUserId)->exists()) {
            $this->likes()->create([
                'user_id' => $currentUserId,
            ]);

            event(new ReplyWasLiked($this->thread, $this));
        }
    }

    /**
     * Unlike the current reply
     *
     * @param integer $userId
     * @return void
     */
    public function unlikedBy($userId = null)
    {
        $currentUserId = $userId ?: auth()->id();
        $this->likes()
            ->where('user_id', $currentUserId)
            ->get()
            ->each
            ->delete();
    }

    /**
     * Get the number of the page a specific reply belongs to
     *
     * @param Thread $thread
     * @return void
     */
    public function getPageNumberAttribute()
    {
        $numberOfRepliesBefore = $this->thread->replies()->where('id', '<=', $this->id)->count();

        return (int) ceil($numberOfRepliesBefore / Reply::PER_PAGE);
    }

    /**
     * Get all the like information for a reply
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeWithLikes(Builder $query)
    {
        return $query->with('likes')
            ->withCount('likes');
    }

    /**
     * Determine whether the reply is liked by the authenticated user
     *
     * @return boolean
     */
    public function getIsLikedAttribute()
    {
        return $this->likes->contains('user_id', auth()->id());
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
            ->withLikes()
            ->filter($filters)
            ->paginate(static::PER_PAGE);

        $replies->each(function ($reply) {
            $reply->append('is_liked');
        });

        return $replies;
    }

    /**
     * Detecte the quoted reply and its poster
     *
     * @return array
     */
    public function quotedReply()
    {
        preg_match_all(
            '^<blockquote> <a href="[^>]+>(\w+) said to post (\d+) </a> </blockquote>',
            $this->body,
            $matches
        );
        return $matches;
    }
}