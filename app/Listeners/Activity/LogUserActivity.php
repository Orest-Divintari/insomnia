<?php

namespace App\Listeners\Activity;

use App\Actions\ActivityLogger;
use App\Activity;
use App\Events\Activity\UserViewedPage;

class LogUserActivity
{

    protected $logger;

    public function __construct(ActivityLogger $logger)
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
        $this->logger->on($event->subject)
            ->type($this->getActivityType($event->subject))
            ->description($event->description)
            ->log();
    }

    /**
     * Get the activity type
     *
     * @param mixed $subject
     * @return string
     */
    public function getActivityType($subject)
    {
        $type = 'viewed-';
        if ($subject) {
            $type .= strtolower(class_basename($subject));
        } else {
            $type .= 'page';
        }
        return $type;
    }
}