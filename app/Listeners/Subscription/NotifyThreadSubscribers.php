<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\NewReplyWasPostedToThread;
use App\Listeners\Notify;
use App\Models\User;
use App\Notifications\ThreadHasNewReply;
use App\Traits\HandlesNotifications;

class NotifyThreadSubscribers
{

    use HandlesNotifications;

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
                $this->notify($subscription->user, $this->notification($event));
            });
    }

    /**
     * Create a new notification instance
     *
     * @param NewReplyWasPostedToThread $event
     * @return ThreadHasNewReply
     */
    public function notification($event)
    {
        return new ThreadHasNewReply($event->thread, $event->reply);
    }
}