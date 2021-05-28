<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\ProfilePostWasLiked;
use App\Notifications\ProfilePostHasNewLike;

class NotifyProfilePostPosterOfNewLike
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
     * @param  ProfilePostWasLiked  $event
     * @return void
     */
    public function handle(ProfilePostWasLiked $event)
    {
        if ($event->poster->isNot($event->liker)) {

            $event->poster->notify($this->notification($event));
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