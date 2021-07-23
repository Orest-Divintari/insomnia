<?php

namespace Tests\Setup;

use App\Models\Conversation;
use App\Models\Reply;

class MessageFactory extends PostFactory
{
    protected $conversation;

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $message = Reply::factory()->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'repliable_id' => $this->conversationId(),
                    'repliable_type' => Conversation::class,
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $message;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->attributes = $attributes;
        $messages = Reply::factory()->count($count)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'repliable_id' => $this->conversationId(),
                    'repliable_type' => Conversation::class,
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $messages;
    }

    private function conversationId()
    {
        if ($this->repliableIdInAttributes()) {
            return;
        }
        return $this->conversation->id ?? Conversation::factory()->create()->id;
    }

    public function toConversation($conversation)
    {
        $this->conversation = $conversation;
        return $this;
    }

}