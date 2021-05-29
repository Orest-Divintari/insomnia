<?php

namespace App\Traits;

use App\Events\LikeEvent;
use App\Events\Like\PostWasUnliked;
use App\Helpers\ModelType;
use App\Like;
use Illuminate\Database\Eloquent\Builder;

trait Likeable
{
/**
 * A likeable model has likes
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Like the given likeable model
     *
     * @param User $user
     * @return Like
     */
    public function likedBy($user = null)
    {
        $liker = $user ?: auth()->user();
        $likerId = $liker->id;

        if (!$this->likes()->where('liker_id', $likerId)->exists()) {

            $like = $this->likes()->create([
                'liker_id' => $likerId,
                'likee_id' => $this->poster->id,
                'type' => ModelType::like($this),

            ]);

            event((new LikeEvent($liker, $this, $like))->create());

            return $like;
        }
    }

    /**
     * Unlike the given likeable model
     *
     * @param integer $userId
     * @return void
     */
    public function unlikedBy($user = null)
    {
        $liker = $user->id ?: auth()->id();

        $like = $this->likes()
            ->where('liker_id', $liker)
            ->first();

        $likeId = $like->id;
        $like->delete();

        event(new PostWasUnliked($this, $likeId));
    }

    /**
     * Get all the like information for a likeable model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeWithLikes(Builder $query)
    {
        return $query
            ->withCount('likes')
            ->withIsLikedByAuthUser();
    }

    /**
     * Add a column which determines whether the likeable model has been liked by
     * the authenticated user
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithIsLikedByAuthUser($query)
    {
        $tableName = $this->getTable();

        return $query->selectRaw('
            EXISTS
            (
                SELECT *
                FROM   likes
                WHERE  likes.likeable_id=' . "{$tableName}.id" . '
                AND    likes.likeable_type=?
                AND    likes.liker_id =?
                AND    likes.likee_id =' . "{$tableName}.user_id" . '
            ) AS is_liked',
            [get_class($this), auth()->id()]
        );
    }

}