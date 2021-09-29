<?php

namespace App\Listeners\Profile;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Notifications\APostOnYourProfileHasNewComment;
use App\Traits\HandlesNotifications;

class NotifyProfileOwnerOfNewCommentOnAPost
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
        $profileOwner = $event->profileOwner;
        $profilePostPoster = $event->profilePost->poster;
        $commentPoster = $event->commentPoster;

        if ($profileOwner->isNot($profilePostPoster)
            && $profileOwner->isNot($commentPoster)
        ) {
            $this->notify($profileOwner, $this->notification($event));
        }
    }

    /**
     * Get the notification instance
     *
     * @param NewCommentWasAddedToProfilePost $event
     * @return APostOnYourProfileHasNewComment
     */
    protected function notification($event)
    {
        return new APostOnYourProfileHasNewComment(
            $event->profilePost,
            $event->comment,
            $event->commentPoster,
            $event->profileOwner
        );
    }
}