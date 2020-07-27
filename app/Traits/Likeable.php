<?php

namespace App\Traits;

use App\Events\Subscription\ReplyWasLiked;
use App\Like;
use Illuminate\Database\Eloquent\Builder;

trait Likeable
{
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
     * Determine whether the reply is liked by the authenticated user
     *
     * @return boolean
     */
    public function getIsLikedAttribute()
    {
        return $this->likes->contains('user_id', auth()->id());
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

}