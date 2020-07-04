<?php

namespace App;

use App\Events\Subscription\ReplyWasLiked;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{

    const PER_PAGE = 5;

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

            event(new ReplyWasLiked($this, $this->thread));
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
     * @param Builder $builder
     * @return void
     */
    public function scopeWithLikes(Builder $builder)
    {
        return $builder->with('likes')
            ->withCount('likes')
            ->addSelect(['is_liked' => Like::select(
                DB::raw('CASE WHEN count(likes.id) > 0 THEN TRUE ELSE FALSE END')
            )->whereColumn('replies.id', '=', 'likes.reply_id')
                    ->groupBy('likes.reply_id'),
            ]);
    }

}