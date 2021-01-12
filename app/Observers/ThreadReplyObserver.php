<?php

namespace App\Observers;

use App\Reply;

class ThreadReplyObserver
{

    /**
     * Handle the reply "creating" event.
     *
     * @param  \App\Reply  $threadReply
     * @return void
     */
    public function creating(Reply $reply)
    {
        $thread = $reply->repliable;
        if ($reply->isThreadReply() && !$reply->isThreadBody()) {
            $thread->increment('replies_count');
            $reply->position = $thread->replies_count + 1;
        }
    }

    /**
     * Handle the reply "deleting" event.
     *
     * @param  \App\Reply  $reply
     * @return void
     */
    public function deleting(Reply $reply)
    {
        $thread = $reply->repliable;
        if ($reply->isThreadReply()
            && !$reply->isThreadBody()
        ) {
            $thread->decrement('replies_count');
        }
    }

}