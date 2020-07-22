<?php

namespace App\Policies;

use App\ProfilePost;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePostPolicy
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
     * Determine if the user can manage the given profile post
     *
     * @param User $user
     * @param ProfilePost $post
     * @return bool
     */
    public function manage(User $user, ProfilePost $post)
    {
        return $user->is($post->poster);
    }
}