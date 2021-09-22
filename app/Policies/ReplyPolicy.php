<?php

namespace App\Policies;

use App\Http\Middleware\MustBeVerified;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the reply
     *
     * @param User $user
     * @param Reply $reply
     * @return boolean
     */
    public function update(User $user, Reply $reply)
    {
        return $reply->poster->is($user);
    }

    /**
     * Determine whether a user can create a reply
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user && $user->hasVerifiedEmail();
    }

    /**
     * Determine whether a user can delete a reply
     *
     * @param User $user
     * @param Reply $reply
     * @return boolean
     */
    public function delete(User $user, Reply $reply)
    {
        if ($reply->isThreadReply()) {
            return $this->canDeleteThreadReply($user, $reply);
        } elseif ($reply->isComment()) {
            return $this->canDeleteComment($user, $reply);
        }
    }

    /**
     * Determine whether the user can delete a thread reply
     *
     * @param User $user
     * @param Reply $reply
     * @return boolean
     */
    public function canDeleteThreadReply($user, $reply)
    {
        return $reply->poster->is($user);
    }
    /**
     * Determine whether a user can delete a profile post comment
     *
     * @param User $user
     * @param Reply $comment
     * @return boolean
     */
    public function canDeleteComment($user, $comment)
    {
        return $comment->poster->is($user)
        || $comment->repliable->profileOwner->is($user);
    }

    /**
     * Determine whether the user can like the reply
     *
     * @param User $user
     * @param Reply $reply
     * @return mixed
     */
    public function like(User $user, Reply $reply)
    {
        return !$reply->isLiked($user) && $user->hasVerifiedEmail() ?:
        $this->deny(MustBeVerified::EXCEPTION_MESSAGE);
    }
}