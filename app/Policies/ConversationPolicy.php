<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Conversation  $conversation
     * @return mixed
     */
    public function view(User $user, Conversation $conversation)
    {
        return $conversation->hasParticipant($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Conversation  $conversation
     * @return mixed
     */
    public function update(User $user, Conversation $conversation)
    {
        return $user->is($conversation->starter);
    }

    /**
     * Determine whether the user can set another participant as admin
     * Determine whether the user can invite/remove another participant to/from the conversation
     *
     * @param User $user
     * @param Conversation $conversation
     * @return mixed
     */
    public function manage(User $user, Conversation $conversation)
    {
        if (!$conversation->hasParticipant($user)) {
            return false;
        }

        return $user->is($conversation->starter) || $conversation->isAdmin($user);
    }

    /**
     * Determine whether the authenticated user can start a conversation with the given user
     *
     * @param User|null $authUser
     * @param User|null $user
     * @return mixed
     */
    public function create(User | null $authUser, User | null $user = null)
    {
        return $authUser->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can view the start conversation button
     *
     * @param User|null $authUser
     * @param User $user
     * @return void
     */
    public function view_start_conversation_button(?User $authUser, User $user)
    {
        return isset($authUser)
        && $authUser->hasVerifiedEmail()
        && $user->hasVerifiedEmail()
        && $authUser->isNot($user)
        && $user->allows('start_conversation');
    }

    /**
     * Determine whether the user can add a new reply to conversation
     *
     * @param User $user
     * @param Conversation $conversation
     * @return mixed
     */
    public function add_reply(User $user, Conversation $conversation)
    {
        return $user->hasVerifiedEmail()
        && !$conversation->isLocked()
        && $conversation->hasParticipant($user);
    }
}