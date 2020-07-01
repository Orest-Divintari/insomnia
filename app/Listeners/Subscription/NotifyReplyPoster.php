<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\ReplyWasLiked;
use App\Notifications\ReplyHasNewLike;
use App\Reply;

class NotifyReplyPoster
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
     * @param  ReplyWasLiked  $event
     * @return void
     */
    public function handle(ReplyWasLiked $event)
    {
        $poster = $event->reply->poster;

        if (auth()->id() == $poster->id || !$event->thread->isSubscribedBy($poster->id)) {
            return;
        }

        $poster->notify(new ReplyHasNewLike($event->reply));

    }
}