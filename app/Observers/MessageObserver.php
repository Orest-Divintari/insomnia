<?php

namespace App\Observers;

use App\Reply;

class MessageObserver
{

    /**
     * Handle the reply "created" event.
     *
     * @param  \App\Reply  $reply
     * @return void
     */
    public function created(Reply $reply)
    {
        $conversation = $reply->repliable;
        if ($reply->isMessage()) {
            $conversation->unhide();
            $conversation->read($reply->poster);
        }
    }
}