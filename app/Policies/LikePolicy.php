<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the like button
     *
     * @param User $user
     * @return mixed
     */
    public function view_button(User $user)
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can create a like
     *
     * @param  User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasVerifiedEmail();
    }
}