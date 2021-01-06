<?php

namespace App\Observers;

use App\Reply;

class ReplyObserver
{

    /**
     * Handle the reply "deleted" event.
     *
     * @param  \App\Reply  $reply
     * @return void
     */
    public function deleting(Reply $reply)
    {
        if ($reply->isThreadReply()
            && $reply->repliable->hasReplies()
        ) {
            $reply->repliable->decrement('replies_count');
        }

        $reply->likes->each->delete();
        $reply->activities->each->delete();
    }

    /**
     * Handle the reply "created" event.
     *
     * @param  \App\Reply  $reply
     * @return void
     */
    public function created(Reply $reply)
    {
        if ($reply->isMessage()) {
            $reply->repliable->unhide();
            $reply->poster->read($reply->repliable);
        }
    }

}