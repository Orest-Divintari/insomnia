<?php

namespace App\Traits;

use App\User;

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
        $this->follows()->save($user);
    }

    /**
     * Unfollow the given user
     *
     * @param User $user
     * @return void
     */
    public function unfollow(User $user)
    {
        $this->follows()->detach($user);
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
        return $this->belongsToMany(User::class, 'follows', 'following_user_id', 'user_id');
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
     * Determine whether the user is followed
     *  by the authenticated/visitor user
     *
     * @return bool
     */
    public function getFollowedByVisitorAttribute()
    {
        return auth()->user()->following($this);
    }

}