<?php

namespace App\Listeners\Follow;

use App\Events\Follow\AUserStartedFollowingYou;
use App\Notifications\YouHaveANewFollower;
use App\Traits\HandlesNotifications;

class NotifyFollowingUser
{

    use HandlesNotifications;

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
        $this->notify($event->following, $this->notification($event));
    }

    /**
     * Create a new notification instance
     *
     * @param AUserStartedFollowingYou $event
     * @return YouHaveANewFollower
     */
    public function notification($event)
    {
        return new YouHaveANewFollower(
            $event->follower,
            $event->following,
            $event->followDate
        );
    }
}