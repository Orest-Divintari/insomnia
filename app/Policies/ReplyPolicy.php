<?php

namespace App\Policies;

use App\Reply;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Reply  $reply
     * @return mixed
     */
    public function delete(User $user, Reply $reply)
    {
        //
    }

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
     * Determine whether a user can delete a comment
     *
     * @param User $user
     * @param Reply $comment
     * @return boolean
     */
    public function deleteComment(User $user, Reply $comment)
    {
        return $comment->poster->is($user) || $comment->repliable->profileOwner->is($user);
    }

}