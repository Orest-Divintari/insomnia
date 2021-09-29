<?php

namespace App\Observers;

use App\Models\Thread;

class SubscribeToThreadObserver
{
    /**
     * Handle the thread "created" event.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function created(Thread $thread)
    {
        $user = auth()->user();

        if ($user && $user->preferences()->subscribe_on_creation) {

            $prefersEmail = $user
                ->preferences()
                ->subscribe_on_creation_with_email;

            $prefersEmail ?
            $thread->subscribe(auth()->id()) :
            $thread->subscribeWithoutEmailNotifications(auth()->id());
        }
    }
}