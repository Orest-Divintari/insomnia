<?php

namespace App\Observers;

use App\Reply;
use App\Thread;

class ThreadObserver
{
    /**
     * Handle the thread "created" event.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function created(Thread $thread)
    {

        $reply = new Reply();
        $reply->setTouchedRelations([]);
        $reply->body = $thread->body;
        $reply->user_id = $thread->user_id;
        $reply->updated_at = $thread->updated_at;
        $reply->created_at = $thread->created_at;
        $reply->repliable_id = $thread->id;
        $reply->position = 1;
        $reply->repliable_type = 'App\Thread';
        $reply->save();

        if (auth()->check()) {
            $thread->subscribe();
        }
    }

    /**
     * Handle the thread "updated" event.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function updated(Thread $thread)
    {
        //
    }

    /**
     * Handle the thread "deleted" event.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function deleted(Thread $thread)
    {
        //
    }

    /**
     * Handle the thread "restored" event.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function restored(Thread $thread)
    {
        //
    }

    /**
     * Handle the thread "force deleted" event.
     *
     * @param  \App\Thread  $thread
     * @return void
     */
    public function forceDeleted(Thread $thread)
    {
        //
    }
}