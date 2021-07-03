<?php

namespace App\Traits;

use App\Thread;
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
        $this->subscriptions()->updateOrCreate([
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
     * Add column that determines whether the authenticated user has subscribed to the thread
     *
     * @param Builder $query
     * @param User|null $authUser
     * @return Builder
     */
    public function scopeWithSubscribed($query, $authUser)
    {

        return $query->when(isset($authUser), function ($query) use ($authUser) {
            return $query->selectRaw('EXISTS
                        (
                            SELECT *
                            FROM   thread_subscriptions
                            WHERE  user_id=?
                            AND    thread_id=threads.id
                        ) AS subscribed', [$authUser->id]
            );
        });
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