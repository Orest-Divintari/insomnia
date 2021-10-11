<?php

namespace App\Listeners\Profile;

use App\Actions\NotifyMentionedUsersAction;
use App\Events\Profile\CommentWasUpdated;
use App\Events\Profile\NewCommentWasAddedToProfilePost;
use App\Notifications\YouHaveBeenMentionedInAComment;
use App\Traits\HandlesNotifications;
use Egulias\EmailValidator\Warning\Comment;

class NotifyMentionedUsersInComment
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
     * @param  NewCommentWasAddedToProfilePost|CommentWasUpdated  $event
     * @return void
     */
    public function handle(NewCommentWasAddedToProfilePost | CommentWasUpdated $event)
    {
        $action = new NotifyMentionedUsersAction(
            $event->comment,
            $event,
            $this->notification($event)
        );
        $action->execute();
    }

    /**
     * Get the notification
     *
     * @param NewCommentWasAddedToProfilePost|CommentWasUpdated $event
     * @return YouHaveBeenMentionedInAComment
     */
    protected function notification($event)
    {
        return new YouHaveBeenMentionedInAComment(
            $event->profilePost,
            $event->comment,
            $event->poster,
            $event->profileOwner
        );
    }
}