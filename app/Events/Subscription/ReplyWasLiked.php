<?php

namespace App\Events\Subscription;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyWasLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thread;
    public $reply;
    public $liker;
    public $like;
    /**
     * Create a new event instance.
     *
     * @param User $liker
     * @param Like $like
     * @param Thread $thread
     * @param Reply $reply
     *
     * @return void
     */
    public function __construct($liker, $like, $thread, $reply)
    {
        $this->liker = $liker;
        $this->like = $like;
        $this->thread = $thread;
        $this->reply = $reply;
    }
}