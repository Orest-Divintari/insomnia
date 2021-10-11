<?php

namespace App\Listeners\Thread;

use App\Actions\NotifyMentionedUsersAction;
use App\Events\Subscription\NewReplyWasPostedToThread;
use App\Events\Thread\ThreadReplyWasUpdated;
use App\Notifications\YouHaveBeenMentionedInAThreadReply;

class NotifyMentionedUsersInThreadReply
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
     * @param  NewReplyWasPostedToThread|ThreadReplyWasUpdated  $event
     * @return void
     */
    public function handle(NewReplyWasPostedToThread | ThreadReplyWasUpdated $event)
    {
        $action = new NotifyMentionedUsersAction(
            $event->reply,
            $event,
            $this->notification($event)
        );
        $action->execute();
    }

    /**
     * Create a new notification instance
     *
     * @param NewReplyWasPostedToThread|ThreadReplyWasUpdated $event
     * @return YouHaveBeenMentionedInAThreadReply
     */
    protected function notification($event)
    {
        return new YouHaveBeenMentionedInAThreadReply($event->thread, $event->reply, $event->poster);
    }
}