<?php

namespace App\Listeners\Conversation;

use App\Events\Conversation\MessageWasLiked;
use App\Notifications\MessageHasNewLike;
use App\Traits\HandlesNotifications;

class NotifyMessagePoster
{
    use HandlesNotifications;

    /**
     * Handle the event.
     *
     * @param  MessageWasLiked  $event
     * @return void
     */
    public function handle(MessageWasLiked $event)
    {
        if ($event->messagePoster->id !== $event->liker->id) {
            $this->notify($event->messagePoster, $this->notification($event));
        }
    }

    /**
     * Create a new notification instance
     *
     * @param MessageWasLiked $event
     * @return MessageHasNewLike
     */
    public function notification($event)
    {
        return new MessageHasNewLike(
            $event->like,
            $event->liker,
            $event->conversation,
            $event->message
        );
    }
}