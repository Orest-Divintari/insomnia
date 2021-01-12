<?php

namespace App\Traits;

use App\ThreadSubscription;

trait Subscribable
{
/**
 * Subscribe a user to the current thread
 * Enable or disable email notifications for the thread
 *
 * @param int|null $userId
 * @param boolean $prefersEmail
 * @return void
 */
    public function subscribe($userId = null, $prefersEmail = true)
    {
        $this->subscriptions()->updateOrcreate([
            'user_id' => $userId ?? auth()->id(),
            'prefers_email' => $prefersEmail,
        ]);
    }

    public function subscribeWithoutEmails()
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
     * Determine whether a user is subscribed to current thread
     *
     * @param int $userId
     * @return boolean
     */
    public function isSubscribedBy($userId)
    {
        return $this->subscriptions()->where([
            'user_id' => $userId,
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

}