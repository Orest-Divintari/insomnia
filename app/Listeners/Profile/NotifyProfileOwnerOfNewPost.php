<?php

namespace App\Listeners\Profile;

use App\Events\Profile\NewPostWasAddedToProfile;
use App\Notifications\ProfileHasNewPost;
use App\Traits\HandlesNotifications;

class NotifyProfileOwnerOfNewPost
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
     * @param  NewPostWasAddedToProfile  $event
     * @return void
     */
    public function handle(NewPostWasAddedToProfile $event)
    {
        if ($event->profileOwner->isNot($event->postPoster)) {
            $this->notify($event->profileOwner, $this->notification($event));
        }

    }

    /**
     * Createa a new notification instance
     *
     * @param [type] $event
     * @return void
     */
    public function notification($event)
    {
        return new ProfileHasNewPost(
            $event->profilePost,
            $event->postPoster,
            $event->profileOwner
        );
    }
}