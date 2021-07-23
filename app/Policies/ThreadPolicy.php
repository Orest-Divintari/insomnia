<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\User  $user
     * @param  \App\Thread  $thread
     * @return mixed
     */
    public function manage(User $user, Thread $thread)
    {
        return $thread->poster->is($user);
    }

    /**
     * Determine whether the user can lock the model
     *
     * @param User $user
     * @param Thread $thread
     * @return boolean
     */
    public function lock(User $user, Thread $thread)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can pin the thread
     *
     * @param User $user
     * @param Thread $thread
     * @return boolean
     */
    public function pin(User $user, Thread $thread)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can ignore the thread
     *
     * @param User $user
     * @param Thread $thread
     * @return mixed
     */
    public function ignore(?User $user, Thread $thread)
    {
        return $user && $thread->isNotIgnored($user) && $user->isNot($thread->poster);
    }

    /**
     * Determine whether the user can uningore the thread
     *
     * @param User $user
     * @param Thread $thread
     * @return mixed
     */
    public function unignore(?User $user, Thread $thread)
    {
        return $user && $thread->isIgnored($user);
    }

    /**
     * Determine whether the authenticated user can subscribe to the thread
     *
     * @param User $user
     * @param Thread $thread
     * @return mixed
     */
    public function subscribe(User $user, Thread $thread)
    {
        return !$thread->hasSubscriber($user);
    }
}
