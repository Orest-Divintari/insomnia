<?php

namespace App\Listeners\Conversation;

use App\Events\Conversation\MessageWasAdded;
use App\Events\Conversation\NewMessageWasAddedToConversation;
use App\Notifications\ConversationHasNewMessage;

class NotifyConversationParticipants
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
     * @param  MessageWasAdded  $event
     * @return void
     */
    public function handle(NewMessageWasAddedToConversation $event)
    {
        $event->conversation
            ->activeParticipants()
            ->where('user_id', '!=', $event->message->poster->id)
            ->get()
            ->each(function ($participant) use ($event) {
                $participant->notify(
                    new ConversationHasNewMessage(
                        $event->conversation, $event->message
                    )
                );
            });
    }
}