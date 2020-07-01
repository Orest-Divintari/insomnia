<?php

namespace App;

use App\Events\Subscription\ReplyWasLiked;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{

    const PER_PAGE = 3;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_updated',
        'date_created',
        'is_liked',
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
     * Determine whether the reply has been liked
     *
     * @return boolean
     */
    public function getIsLikedAttribute()
    {
        return $this->likes()->exists();
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

}