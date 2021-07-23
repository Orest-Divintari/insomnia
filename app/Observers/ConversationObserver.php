<?php

namespace App\Observers;

use App\Models\Conversation;

class ConversationObserver
{
    /**
     * Handle the conversation "created" event.
     *
     * @param  \App\Conversation  $conversation
     * @return void
     */
    public function created(Conversation $conversation)
    {
        /**
         * When a converastion is created
         * attach the authenticated user as participant and admin
         */
        if (auth()->check()) {
            $conversation->addParticipants(
                [auth()->user()->name],
                $admin = true
            );
        }
    }

    /**
     * Handle the conversatio "deleting" event
     *
     * @param Conversation $conversation
     * @return void
     */
    public function deleting(Conversation $conversation)
    {
        $conversation->reads->each->delete();
    }

}
