<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\ProfilePostWasLiked;
use App\Notifications\ProfilePostHasNewLike;
use App\Traits\HandlesNotifications;

class NotifyProfilePostPosterOfNewLike
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
     * @param  ProfilePostWasLiked  $event
     * @return void
     */
    public function handle(ProfilePostWasLiked $event)
    {
        if ($event->poster->isNot($event->liker)) {

            $this->notify($event->poster, $this->notification($event));
        }
    }

    /**
     * Get an instance of the notification
     *
     * @param ProfilePostWasLiked $event
     * @return ProfilePostHasNewLike
     */
    protected function notification($event)
    {
        return new ProfilePostHasNewLike(
            $event->profilePost,
            $event->poster,
            $event->profileOwner,
            $event->liker,
            $event->like
        );
    }
}