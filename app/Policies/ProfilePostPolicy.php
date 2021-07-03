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

    /**
     * Determine if the user can update the given profile post
     *
     * @param User $user
     * @param ProfilePost $post
     * @return bool
     */
    public function update(User $user, ProfilePost $post)
    {
        return $user->is($post->poster);
    }

    /**
     * Determine whether the user can delete a profile post
     *
     * @param User $user
     * @param ProfilePost $post
     * @return boolean
     */
    public function delete(User $user, ProfilePost $post)
    {
        return $post->poster->is($user) || $post->profileOwner->is($user);
    }

    /**
     * Determine whether the user can like the profile post
     *
     * @param User $user
     * @param ProfilePost $profilePost
     * @return mixed
     */
    public function like(User $user, ProfilePost $profilePost)
    {
        return !$profilePost->isLiked($user);
    }

}