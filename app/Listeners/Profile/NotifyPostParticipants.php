<?php

namespace App\Listeners\Profile;

use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Notifications\ProfilePostHasNewComment;

class NotifyPostParticipants
{
    protected $event;

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
        $this->event = $event;
        $this->notifyParticipants();
        $this->notifyProfileOwner();
        $this->notifyProfilePostOwner();
    }

    /**
     * Send a notification to all users who have participated in the post
     *
     * @param NewCommentWasAddedToProfilePost $event
     * @return void
     */
    public function notifyParticipants()
    {
        $this->event->post->fresh()->comments()
            ->where('user_id', '!=', $this->event->commentPoster->id)
            ->get()
            ->each(function ($comment) {
                $comment->poster->notify(
                    $this->createNotification()
                );
            });

    }

    /**
     * Notify the owner of the profile post
     *
     * @return void
     */
    public function notifyProfilePostOwner()
    {

        $this->event->post->poster
            ->notify($this->createNotification());
    }

    /**
     * Send a notification to the owner of the profile in the case where
     * there is a new comment on another user's post
     *
     * @return void
     */
    public function notifyProfileOwner()
    {
        if ($this->event->post->poster->id != $this->event->profileUser->id) {
            $this->event->profileUser->notify(
                $this->createNotification()
            );
        }
    }

    /**
     * Create a new ProfilePostHasNewComment notification
     *
     * @return void
     */
    public function createNotification()
    {
        return new ProfilePostHasNewComment(
            $this->event->post,
            $this->event->comment,
            $this->event->commentPoster,
            $this->event->profileUser
        );
    }

}