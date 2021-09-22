<?php

namespace App\Policies;

use App\Http\Middleware\MustBeVerified;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**s
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the authenticated user can view the profile of the given user
     *
     * @param User|null $authUser
     * @param User $user
     * @return mixed
     */
    public function view_profile(User $authUser, User $user)
    {
        if ($user->is($authUser)) {
            return true;
        } elseif ($user->hasNotVerifiedEmail()) {
            return $this->deny("This user's profile is not available.", 403);
        } elseif ($authUser->hasNotVerifiedEmail()) {
            return $this->deny(MustBeVerified::EXCEPTION_MESSAGE, 403);
        }
        return $user->allows('show_details') ?:
        $this->deny('This member limits who may view their full profile.', 403);
    }

    /**
     * Determine whether the profile owner allows users to post on profile
     *
     * @param User|null $user
     * @param User $profileOwner
     * @return void
     */
    public function post_on_profile(User $user, User $profileOwner)
    {
        return
        $user->is($profileOwner) || $profileOwner->allows('post_on_profile') ?:
        $this->deny('This member limits who may post on their profile.', 403);
    }

    /**
     * Determine whether the authenticated user can view the identities of the given user
     *
     * @param User|null $authUser
     * @param User $user
     * @return mixed
     */
    public function view_identities(User $authUser, User $user)
    {
        return $user->is($authUser) || $user->allows('show_identities');
    }

    /**
     * Determine whether can ignore the user
     *
     * @param User $authUser
     * @param User $user
     * @return mixed
     */
    public function ignore(User $authUser, User $user)
    {
        return $authUser->hasVerifiedEmail()
        && $user->hasVerifiedEmail()
        && $user->isNot($authUser)
        && $user->isNotIgnored($authUser);
    }

    /**
     * Determine whether can view the ignore button
     *
     * @param User $authUser
     * @param User $user
     * @return mixed
     */
    public function view_ignore_button(User $authUser, User $user)
    {
        return $authUser->hasVerifiedEmail()
        && $user->hasVerifiedEmail()
        && $authUser->isNot($user);
    }

    /**
     * Determine whether can unignore the user
     *
     * @param User $authUser
     * @param User $user
     * @return mixed
     */
    public function unignore(User $authUser, User $user)
    {
        return $user->isIgnored($authUser);
    }

    /**
     * Determine whether the authenticated user can follow the given user
     *
     * @param User $authUser
     * @param User $user
     * @return mixed
     */
    public function follow(User $authUser, User $user)
    {
        return $authUser->hasVerifiedEmail()
        && $user->hasVerifiedEmail()
        && !$authUser->following($user)
        && $authUser->isNot($user);
    }

    /**
     * Determine whether the authenticated user can follow the given user
     *
     * @param User $authUser
     * @param User $user
     * @return mixed
     */
    public function view_follow_button(User $authUser, User $user)
    {
        return $authUser->hasVerifiedEmail()
        && $authUser->isNot($user)
        && $user->hasVerifiedEmail();
    }

}