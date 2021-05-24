<?php

namespace App\Traits;

use App\ThreadSubscription;

trait Subscribable
{

    /**
     * Subscribe a user to the thread
     * Enable email notifications by default
     *
     * @param int|null $userId
     * @return void
     */
    public function subscribe($userId = null)
    {
        return $this->subscriptions()->updateOrcreate([
            'user_id' => $userId ?? auth()->id(),
            'prefers_email' => true,
        ]);
    }

    /**
     * Subscribe a user to the thread
     * Disable email notifications by default
     *
     * @param int $userId
     * @return void
     */
    public function subscribeWithoutEmailNotifications($userId = null)
    {
        $this->subscriptions()->updateOrcreate([
            'user_id' => $userId ?? auth()->id(),
            'prefers_email' => false,
        ]);
    }

    /**
     * Unsubscribe a user from the current thread
     *
     * @param int|null $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()->where([
            'user_id' => $userId ?? auth()->id(),
        ])->delete();
    }

    /**
     * Determine whether the authenicated user has subscribed to current thread
     *
     * @return boolean
     */
    public function getSubscribedByAuthUserAttribute()
    {
        return $this->subscriptions()->where([
            'user_id' => auth()->id(),
        ])->exists();

    }

    /**
     * A thread can have many subscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Determine whether a user has subscribe to the thread
     *
     * @param User $user
     * @return boolean
     */
    public function hasSubscriber($user)
    {
        return $this->subscriptions()
            ->where('user_id', $user->id)
            ->exists();
    }
}