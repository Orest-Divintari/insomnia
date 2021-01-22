<?php

namespace App\Listeners\Profile;

use App\Events\Profile\CommentWasLiked;
use App\Notifications\CommentHasNewLike;
use Egulias\EmailValidator\Warning\Comment;

class NotifyCommentPoster
{
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

        $this->event->commentPoster->notify(
            new CommentHasNewLike(
                $this->event->liker,
                $this->event->like,
                $this->event->comment,
                $this->event->commentPoster,
                $this->event->profilePost,
                $this->event->profileOwner,
            )
        );
    }

    /**
     * Determine whether is the owner of the comment
     *
     */
    public function isOwnerOfComment()
    {
        return auth()->id() == $this->event->commentPoster->id;
    }
}