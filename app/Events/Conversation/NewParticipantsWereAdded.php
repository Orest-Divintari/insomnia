<?php

namespace App\Events\Conversation;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewParticipantsWereAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $participantIds;
    /**
     * Create a new event instance.
     *
     * @param Conversation $conversation
     * @param Collection $participantIds
     *
     * @return void
     */
    public function __construct($conversation, $participantIds)
    {
        $this->conversation = $conversation;
        $this->participantIds = $participantIds;
    }
}