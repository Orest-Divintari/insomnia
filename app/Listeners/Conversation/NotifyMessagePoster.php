<?php

namespace App\Listeners\Conversation;

use App\Events\Converstion\MessageWasLiked;
use App\Notifications\MessageHasNewLike;

class NotifyMessagePoster
{

    /**
     * Handle the event.
     *
     * @param  MessageWasLiked  $event
     * @return void
     */
    public function handle(MessageWasLiked $event)
    {
        if ($event->messagePoster->id !== $event->liker->id) {
            $event->messagePoster->notify(
                new MessageHasNewLike(
                    $event->like,
                    $event->liker,
                    $event->conversation,
                    $event->message
                )
            );
        }
    }
}