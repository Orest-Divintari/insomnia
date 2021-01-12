<?php

namespace Tests\Setup;

use App\Conversation;
use App\Reply;

class MessageFactory extends PostFactory
{
    private $conversation;

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $message = factory(Reply::class)->create(
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
        $messages = factory(Reply::class, $count)->create(
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
        return $this->conversation->id ?? factory(Conversation::class)->create()->id;
    }

    public function toConversation($conversation)
    {
        $this->conversation = $conversation;
        return $this;
    }

}