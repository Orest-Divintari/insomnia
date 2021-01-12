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
        $reply->likes->each->delete();
        $reply->activities->each->delete();
    }
}