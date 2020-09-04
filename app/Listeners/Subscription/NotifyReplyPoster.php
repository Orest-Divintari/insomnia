<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\ReplyWasLiked;
use App\Notifications\ReplyHasNewLike;
use App\Reply;
use App\User;

class NotifyReplyPoster
{
    protected $event;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  ReplyWasLiked  $event
     * @return void
     */
    public function handle(ReplyWasLiked $event)
    {
        $this->event = $event;

        $poster = $this->event->reply->poster;

        if ($this->isOwnerOfReply($poster)) {
            return;
        }

        $poster->notify(new ReplyHasNewLike(
            $this->event->liker,
            $this->event->like,
            $this->event->thread,
            $this->event->reply
        ));

    }

    /**
     * Determine whether the user who liked the reply is the same user who posted the reply
     *
     * @param  ReplyWasLiked $event
     * @param  \App\User $poster
     * @return boolean
     */
    public function isOwnerOfReply($poster)
    {
        return $this->event->liker->id == $poster->id;
    }
}