<?php

namespace App\Events\Conversation;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageWasLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;
    public $messagePoster;
    public $liker;
    public $like;

    /**
     * Create a new event instance.
     *
     * @param User $liker
     * @param Like $like
     * @param Conversation $conversation
     * @param Reply $message
     * @param User $messagePoster
     */
    public function __construct($liker, $like, $conversation, $message, $messagePoster)
    {
        $this->liker = $liker;
        $this->like = $like;
        $this->conversation = $conversation;
        $this->message = $message;
        $this->messagePoster = $messagePoster;
    }
}