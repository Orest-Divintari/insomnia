<?php

namespace App\Events\Thread;

class ThreadReplyWasUpdated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public $thread, public $reply, public $poster)
    {
    }
}