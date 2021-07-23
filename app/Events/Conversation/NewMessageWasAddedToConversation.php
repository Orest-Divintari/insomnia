<?php

namespace App\Events\Conversation;

use App\Models\Conversation;
use App\Models\Reply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageWasAddedToConversation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation, Reply $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;
    }
}
