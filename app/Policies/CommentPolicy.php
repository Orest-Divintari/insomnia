<?php

namespace App\Policies;

use App\Comment;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Reply  $comment
     * @return boolean
     */
    public function update(User $user, Reply $comment)
    {
        return $comment->poster->is($user) || $comment->repliable->poster->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return boolean
     */
    public function delete(User $user, Reply $comment)
    {

    }

}
