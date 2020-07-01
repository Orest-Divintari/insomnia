<?php

namespace App\Events\Subscription;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyWasLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply;
    public $thread;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($reply, $thread)
    {
        $this->reply = $reply;
        $this->thread = $thread;
    }
}