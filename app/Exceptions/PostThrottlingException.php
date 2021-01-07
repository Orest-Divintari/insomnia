<?php

namespace App\Exceptions;

use Exception;

class PostThrottlingException extends Exception
{
    /**
     * The seconds left before next post
     *
     * @var Carbon
     */
    protected $secondsLeftBeforePosting;

    public function __construct($secondsLeftBeforePosting)
    {
        $this->secondsLeftBeforePosting = $secondsLeftBeforePosting;
    }

    public function getSecondsLeftBeforePosting()
    {
        return $this->secondsLeftBeforePosting;
    }

    /**
     * Return the exception message
     *
     * @return string
     */
    public function message()
    {
        return "You must wait at least {$this->getSecondsLeftBeforePosting()} seconds before performing this action.";
    }

    /**
     * Get the error name
     *
     * @return string
     */
    public function error()
    {
        return 'post_throttled';
    }
}