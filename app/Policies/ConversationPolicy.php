<?php

namespace App\Policies;

use App\Conversation;
use App\ConversationParticipant;
use App\User;
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
        return $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
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
     * Determine thether the user can invite/remove another participant to/from the conversation
     *
     * @param User $user
     * @param Conversation $conversation
     * @return mixed
     */
    public function manage(User $user, Conversation $conversation)
    {
        $participant = ConversationParticipant::where('user_id', $user->id)
            ->where('conversation_id', $conversation->id)
            ->first();
        return $user->is($conversation->starter) || ($participant && $participant->admin);
    }

    /**
     * Determine whether the authenticated user can start a conversation with the given user
     *
     * @param User|null $authUser
     * @param User $user
     * @return mixed
     */
    public function create(User $authUser, User $user)
    {
        return $authUser->isNot($user) && $user->allows('start_conversation');
    }
}