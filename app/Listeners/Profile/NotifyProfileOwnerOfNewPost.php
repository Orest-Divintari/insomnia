<?php

namespace App\Listeners\Profile;

use App\Events\Profile\NewPostWasAddedToProfile;
use App\Notifications\ProfileHasNewPost;

class NotifyProfileOwnerOfNewPost
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
     * @param  NewPostWasAddedToProfile  $event
     * @return void
     */
    public function handle(NewPostWasAddedToProfile $event)
    {
        $event->profileUser
            ->notify(new ProfileHasNewPost(
                $event->post,
                $event->poster
            ));
    }
}