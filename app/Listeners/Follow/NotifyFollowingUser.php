<?php

namespace App\Listeners\Follow;

use App\Events\Follow\AUserStartedFollowingYou;
use App\Notifications\YouHaveANewFollower;

class NotifyFollowingUser
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
     * @param  AUserStartedFollowingYou  $event
     * @return void
     */
    public function handle(AUserStartedFollowingYou $event)
    {
        $event->following->notify(
            new YouHaveANewFollower($event->follower, $event->following)
        );
    }
}