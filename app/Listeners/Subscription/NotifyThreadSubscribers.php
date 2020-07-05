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
        $event->thread->subscriptions()
            ->where('user_id', '!=', $event->reply->poster->id)
            ->get()
            ->each(function ($subscription) use ($event) {
                $subscription->user->notify(
                    new ThreadHasNewReply($event->thread, $event->reply)
                );
            });

    }
}