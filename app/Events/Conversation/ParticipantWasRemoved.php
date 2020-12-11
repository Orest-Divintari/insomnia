<?php

namespace App\Events\Conversation;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantWasRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $participantId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conversation, $participantId)
    {
        $this->conversation = $conversation;
        $this->participantId = $participantId;
    }
}