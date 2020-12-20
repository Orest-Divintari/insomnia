<?php

namespace App\Events\Like;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyWasUnliked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $likeId;
    public $reply;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($reply, $likeId)
    {
        $this->reply = $reply;
        $this->likeId = $likeId;
    }
}