<?php

namespace App\Policies;

use App\Thread;
use App\User;
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
}