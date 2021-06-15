<?php

namespace App\Traits;

use App\Events\Follow\AUserStartedFollowingYou;
use App\Events\Follow\AUserUnfollowedYou;
use App\Follow;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Event;

trait Followable
{
    /**
     * Get all following users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function follows()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'user_id',
            'following_user_id'
        );
    }

    /**
     * Follow the given user
     *
     * @param User $user
     * @return void
     */
    public function follow(User $user)
    {
        if (!$this->following($user)) {
            $this->follows()->attach($user->id);
            event(new AUserStartedFollowingYou($this, $user, Carbon::now()));
        }
    }

    /**
     * Unfollow the given user
     *
     * @param User $user
     * @return void
     */
    public function unfollow(User $user)
    {
        $this->follows()->detach($user->id);
        event(new AUserUnfollowedYou($this, $user));
    }

    /**
     * Toggle the follow for the given user
     *
     * @param User $user
     * @return void
     */
    public function toggleFollow(User $user)
    {
        $this->follows()->toggle($user);
    }

    /**
     * Get all followers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followedBy()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_user_id',
            'user_id'
        );
    }

    /**
     * Fetch followers that are not ignored by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function unignoredFollowers()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_user_id',
            'user_id'
        )->whereNotIn('follows.user_id', $this->ignoredUserIds());
    }

    /**
     * Determine whether is following the given user
     *
     * @param User $user
     * @return bool
     */
    public function following(User $user)
    {
        return $this->follows()
            ->where('following_user_id', $user->id)
            ->exists();
    }

    /**
     * Add a column that determines whether the user is followed by the visitor
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWithFollowedByVisitor($query)
    {
        return $query
            ->selectRaw('EXISTS
                (
                    SELECT *
                    FROM   follows
                    WHERE  follows.user_id=?
                    AND    follows.following_user_id=users.id
                ) AS followed_by_visitor', [auth()->id()]);
    }

}