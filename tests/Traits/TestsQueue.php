<?php

namespace Tests\Traits;

use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

trait TestsQueue
{
    /**
     * Assert that the given notification is pushed on the given qeueue
     *
     * @param string $queue
     * @param Notification $notification
     * @return void
     */
    public function assertNotificationPushedOnQueue($queue, $notification)
    {
        Queue::assertPushed(function (SendQueuedNotifications $job) use ($queue, $notification) {
            return get_class($job->notification) === $notification
            && $job->queue === $queue;
        });
    }
}