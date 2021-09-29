<?php

namespace App\Events\Thread;

class ThreadWasCreated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public $thread, public $threadPoster)
    {
    }
}