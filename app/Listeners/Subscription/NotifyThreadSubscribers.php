<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\NewReplyWasPostedToThread;
use App\Listeners\Notify;
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
        $thread = $event->thread;

        $thread->subscribers()
            ->verified()
            ->except($event->poster)
            ->notIgnoring($event->poster)
            ->with(['subscriptions' => function ($query) use ($thread) {
                return $query->where('thread_id', $thread->id);
            }])
            ->get()
            ->each(function ($subscriber) use ($event) {
                $subscriber->notify($this->notification($event));
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