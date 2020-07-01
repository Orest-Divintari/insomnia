<?php

namespace App\Events\Subscription;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReplyWasPostedToThread
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thread;
    public $reply;

    /**
     * Create a new event instance.
     *
     * @param $thread
     * @param $reply
     * @return void
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }
}