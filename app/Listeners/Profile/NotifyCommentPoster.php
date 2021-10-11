<?php

namespace App\Listeners\Profile;

use App\Events\Profile\CommentWasLiked;
use App\Listeners\Notify;
use App\Notifications\CommentHasNewLike;
use App\Traits\HandlesNotifications;
use Egulias\EmailValidator\Warning\Comment;

class NotifyCommentPoster
{

    use HandlesNotifications;

    public $event;
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
     * @param  CommentWasLiked  $event
     * @return void
     */
    public function handle(CommentWasLiked $event)
    {
        $this->event = $event;

        if ($this->isOwnerOfComment()) {
            return;
        }

        $this->notify($event->poster, $this->notification());
    }

    /**
     * Determine whether is the owner of the comment
     *
     */
    public function isOwnerOfComment()
    {
        return auth()->id() == $this->event->poster->id;
    }

    /**
     * Create a new notification instance
     *
     * @return CommentHasNewLike
     */
    public function notification()
    {
        return new CommentHasNewLike(
            $this->event->liker,
            $this->event->like,
            $this->event->comment,
            $this->event->poster,
            $this->event->profilePost,
            $this->event->profileOwner,
        );
    }
}