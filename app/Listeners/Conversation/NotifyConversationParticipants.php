<?php

namespace App\Listeners\Conversation;

use App\Events\Conversation\MessageWasAdded;
use App\Events\Conversation\NewMessageWasAddedToConversation;
use App\Notifications\ConversationHasNewMessage;
use App\Traits\HandlesNotifications;

class NotifyConversationParticipants
{
    use HandlesNotifications;

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
                $this->notify($participant, $this->notification($event));
            });
    }

    /**
     * Create a new notification instance
     *
     * @param NewMessageWasAddedToConversation $event
     * @return ConversationHasNewMessage
     */
    public function notification($event)
    {
        return new ConversationHasNewMessage(
            $event->conversation, $event->message
        );
    }
}