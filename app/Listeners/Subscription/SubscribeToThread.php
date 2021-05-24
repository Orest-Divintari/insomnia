<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\NewReplyWasPostedToThread;

class SubscribeToThread
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
     * @param  NewReplyWasPostedToThread  $event
     * @return void
     */
    public function handle(NewReplyWasPostedToThread $event)
    {
        $user = auth()->user();

        if ($user && $user->preferences()->subscribe_on_interaction) {

            $prefersEmail = $user->preferences()->subscribe_on_interaction_with_email;

            $prefersEmail ?
            $event->thread->subscribe(auth()->id()) :
            $event->thread->subscribeWithoutEmailNotifications(auth()->id());
        }
    }
}