<?php

namespace App\Listeners\Thread;

use App\Actions\NotifyMentionedUsersAction;
use App\Events\Thread\ThreadWasCreated;
use App\Notifications\YouHaveBeenMentionedInAThread;
use App\Traits\HandlesNotifications;

class NotifyMentionedUsersInThread
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
     * @param  NewReplyWasPostedToThread  $event
     * @return void
     */
    public function handle(ThreadWasCreated $event)
    {
        $action = new NotifyMentionedUsersAction(
            $event->thread,
            $event,
            $this->notification($event)
        );
        $action->execute();
    }

    /**
     * Create a new notification instance
     *
     * @param ThreadWasCreated $event
     * @return YouHaveBeenMentionedInAThread
     */
    protected function notification($event)
    {
        return new YouHaveBeenMentionedInAThread($event->thread, $event->threadPoster);
    }
}