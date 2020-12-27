<?php

namespace App\Policies;

use App\Reply;
use App\User;
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
}