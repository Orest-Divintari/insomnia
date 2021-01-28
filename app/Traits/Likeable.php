<?php

namespace App\Traits;

use App\Events\LikeEvent;
use App\Events\Like\ReplyWasUnliked;
use App\Like;
use App\Reply;
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
     * @param User $$user
     * @return Like
     */
    public function likedBy($user = null)
    {
        $liker = $user ?: auth()->user();
        $likerId = $liker->id;

        if (!$this->likes()->where('user_id', $likerId)->exists()) {
            $like = $this->likes()->create([
                'user_id' => $likerId,
            ]);

            event((new LikeEvent($liker, $this, $like))->create());
            return $like;
        }
    }

    /**
     * Unlike the current reply
     *
     * @param integer $userId
     * @return void
     */
    public function unlikedBy($user = null)
    {
        $currentUserId = $user->id ?: auth()->id();

        $like = $this->likes()
            ->where('user_id', $currentUserId)
            ->first();
        $likeId = $like->id;
        $like->delete();

        event(new ReplyWasUnliked($this, $likeId));
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