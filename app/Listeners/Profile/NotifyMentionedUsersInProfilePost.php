<?php

namespace App\Listeners\Profile;

use App\Actions\NotifyMentionedUsersAction;
use App\Events\Profile\NewPostWasAddedToProfile;
use App\Events\Profile\ProfilePostWasUpdated;
use App\Notifications\YouHaveBeenMentionedInAProfilePost;
use App\Traits\HandlesNotifications;

class NotifyMentionedUsersInProfilePost
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
     * @param  NewPostWasAddedToProfile|ProfilePostWasUpdated  $event
     * @return void
     */
    public function handle(NewPostWasAddedToProfile | ProfilePostWasUpdated $event)
    {
        $action = new NotifyMentionedUsersAction(
            $event->profilePost,
            $event,
            $this->notification($event)
        );
        $action->execute();
    }

    /**
     * Get the notification
     *
     * @param NewPostWasAddedToProfile $event
     * @return YouHaveBeenMentionedInAProfilePost
     */
    protected function notification($event)
    {
        return new YouHaveBeenMentionedInAProfilePost(
            $event->profilePost,
            $event->postPoster,
            $event->profileOwner
        );
    }
}