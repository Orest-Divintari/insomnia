<?php

namespace App\Listeners\Like;

use App\Events\Like\ReplyWasUnliked;

class DeleteReplyLikeNotification
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
     * @param  ReplyWasUnliked  $event
     * @return void
     */
    public function handle(ReplyWasUnliked $event)
    {
        $event->reply->poster
            ->notifications()
            ->whereJsonContains('data->like->id', $event->likeId)
            ->delete();
    }
}