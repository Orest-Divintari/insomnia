<?php

namespace App\Listeners\Activity;

use App\Actions\LogOnlineUserActivityAction;
use App\Events\Activity\UserViewedPage;
use App\Models\Activity;

class LogOnlineUserActivity
{

    protected $logger;

    public function __construct(LogOnlineUserActivityAction $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle the event.
     *
     * @param  UserActivity  $event
     * @return void
     */
    public function handle(UserViewedPage $event)
    {
        $this->logger->execute($event->description, $event->subject);
    }

}