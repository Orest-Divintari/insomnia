<?php

namespace App\Traits;

use App\Events\Profile\CommentWasLiked;
use App\Events\Subscription\ReplyWasLiked;
use App\Like;
use App\ProfilePost;
use App\Thread;
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

            if ($this->isComment()) {
                event(
                    new CommentWasLiked(
                        $liker,
                        $like,
                        $this,
                        $this->poster,
                        $this->profilePost,
                        $this->profilePost->profileOwner
                    ));
            } elseif ($this->isThreadReply()) {
                event(
                    new ReplyWasLiked(
                        $liker,
                        $like,
                        $this->thread,
                        $this
                    ));
            }
            return $like;
        }
    }

    /**
     * Determine whether it is a comment of a profile post
     *
     * @return boolean
     */
    public function isComment()
    {
        return $this->repliable_type == ProfilePost::class;
    }

    /**
     * Determine whether it is a thread reply
     *
     * @return boolean
     */
    public function isThreadReply()
    {
        return $this->repliable_type == Thread::class;
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