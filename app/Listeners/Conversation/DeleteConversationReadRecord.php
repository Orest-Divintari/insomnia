<?php

namespace App\Listeners\Conversation;

use App\Events\Conversation\ParticipantWasRemoved;

class DeleteConversationReadRecord
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ParticipantWasRemoved  $event
     * @return void
     */
    public function handle(ParticipantWasRemoved $event)
    {
        $event->conversation->reads()
            ->where('user_id', $event->participantId)
            ->delete();
    }
}