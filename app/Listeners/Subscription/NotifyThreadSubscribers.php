<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\NewReplyWasPostedToThread;
use App\Notifications\ThreadHasNewReply;

class NotifyThreadSubscribers
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
     * @param  NewReplyWasPostedToThread  $event
     * @return void
     */
    public function handle(NewReplyWasPostedToThread $event)
    {
        $event->thread->subscribers()
            ->where('user_id', '!=', $event->reply->poster->id)
            ->get()
            ->each
            ->notify(new ThreadHasNewReply($event->thread, $event->reply));
    }
}