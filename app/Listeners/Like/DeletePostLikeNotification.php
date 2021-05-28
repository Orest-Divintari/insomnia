<?php

namespace App\Listeners\Like;

use App\Events\Like\PostWasUnliked;

class DeletePostLikeNotification
{
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
     * @param  PostWasUnliked  $event
     * @return void
     */
    public function handle(PostWasUnliked $event)
    {
        $event->post->poster
            ->notifications()
            ->whereJsonContains('data->like->id', $event->likeId)
            ->delete();
    }
}