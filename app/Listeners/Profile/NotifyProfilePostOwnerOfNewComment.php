<?php

namespace App\Listeners\Profile;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Notifications\YourProfilePostHasNewComment;
use App\Traits\HandlesNotifications;

class NotifyProfilePostOwnerOfNewComment
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
     * @param  NewCommentWasAddedToProfilePost  $event
     * @return void
     */
    public function handle(NewCommentWasAddedToProfilePost $event)
    {
        $profilePostPoster = $event->profilePost->poster;

        if ($profilePostPoster->isNot($event->profileOwner)
            && $profilePostPoster->isNot($event->commentPoster)
        ) {
            $this->notify($profilePostPoster, $this->notification($event));
        }

    }

    /**
     * Get the notification instance
     *
     * @param NewCommentWasAddedToProfilePost $event
     * @return YourProfilePostHasNewComment
     */
    protected function notification($event)
    {
        return new YourProfilePostHasNewComment(
            $event->profilePost,
            $event->comment,
            $event->commentPoster,
            $event->profileOwner
        );
    }
}