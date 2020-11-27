<?php

namespace App\Listeners\Conversation;

use App\Events\Conversation\NewParticipantsWereAdded;

class MarkConversationAsUnread
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
     * @param  NewParticipantsWereAdded  $event
     * @return void
     */
    public function handle(NewParticipantsWereAdded $event)
    {
        $participantReads = $event->participantIds
            ->map(function ($participantId) {
                return ['user_id' => $participantId];
            });

        $event->conversation
            ->reads()
            ->createMany($participantReads);
    }
}