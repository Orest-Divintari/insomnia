<?php

namespace App\Listeners\Profile;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Notifications\ParticipatedProfilePostHasNewComment;
use App\ProfilePost;
use App\Traits\HandlesNotifications;

class NotifyPostParticipantsOfNewComment
{
    use HandlesNotifications;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  NewCommentWasAddedToProfilePost  $event
     * @return void
     */
    public function handle(NewCommentWasAddedToProfilePost $event)
    {
        $comments = $this->getComments($event);

        $this->notifyParticipants($comments, $event);
    }

    /**
     * Send a notification to all users who have participated in the post
     *
     * @param Reply $comments
     * @param NewCommentWasAddedToProfilePost $event
     * @return void
     */
    public function notifyParticipants($comments, $event)
    {
        $comments->each(function ($comment) use ($event) {
            $this->notify($comment->poster, $this->notification($event));
        });
    }

    /**
     * Get all the comments associated with the profile post
     *
     * @param NewCommentWasAddedToProfilePost $event
     * @return Reply $comments
     */
    public function getComments($event)
    {
        return $event->profilePost->comments()
            ->whereNotIn('user_id', [
                $event->commentPoster->id,
                $event->profilePost->poster->id,
                $event->profileOwner->id]
            )->get();
    }

    /**
     * Get the notification
     *
     * @param NewCommentWasAddedToProfilePost $event
     * @return ParticipatedProfilePostHasNewComment
     */
    protected function notification($event)
    {
        return new ParticipatedProfilePostHasNewComment(
            $event->profilePost,
            $event->comment,
            $event->commentPoster,
            $event->profileOwner
        );
    }
}