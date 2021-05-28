<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\PostWasLiked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPosterOfNewLike
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
     * @param  PostWasLiked  $event
     * @return void
     */
    public function handle(PostWasLiked $event)
    {
        //
    }
}
