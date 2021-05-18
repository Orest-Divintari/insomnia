<?php

namespace App\Policies;

use App\Activity;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the current activity of online users pa should be visible
     *
     * @param User|null $authUser
     * @param Activity $activity
     * @return mixed
     */
    public function view_current(User $authUser, ?User $user)
    {
        return !$user ?: $user->allows('show_current_activity');
    }
}