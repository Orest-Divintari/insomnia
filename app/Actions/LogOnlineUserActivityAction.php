<?php

namespace App\Actions;

use App\Actions\ActivityLogger;
use Illuminate\Http\Request;

class LogOnlineUserActivityAction
{

    /*
     * @var ActivityLogger
     */
    protected $logger;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Create a new instance
     *
     * @param ActivityLogger $logger
     * @param Reqest $request
     */
    public function __construct(ActivityLogger $logger, Request $request)
    {
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * Record the online activity of a user
     *
     * @param string $description
     * @param string|null $subject
     * @return void
     */
    public function execute($description, $subject = null)
    {
        $user = $this->request->user();

        if ($user && $user->hasNotVerifiedEmail()) {
            return;
        }

        $this->logger
            ->on($subject)
            ->type($this->getActivityType($subject))
            ->description($description)
            ->log();
    }

    /**
     * Get the activity type
     *
     * @param mixed $subject
     * @return string
     */
    private function getActivityType($subject)
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