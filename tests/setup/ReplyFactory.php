<?php

namespace Tests\Setup;

use App\Reply;
use App\Thread;
use Tests\Setup\PostFactory;

class ReplyFactory extends PostFactory
{
    private $thread;
    private $replies = [];

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $reply = factory(Reply::class)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'repliable_id' => $this->threadId(),
                    'created_at' => $this->getCreatedAt(),
                    'updated_at' => $this->getUpdatedAt(),
                    'repliable_type' => Thread::class,
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $reply;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->attributes = $attributes;
        $replies = [];
        for ($repliesCounter = 1; $repliesCounter <= $count; $repliesCounter++) {

            $reply = factory(Reply::class)->create(
                array_merge(
                    [
                        'user_id' => $this->userId(),
                        'repliable_id' => $this->threadId(),
                        'repliable_type' => Thread::class,
                        'body' => $this->getBody(),
                    ],
                    $attributes
                ));
            $replies[] = $reply;
        }
        return collect($replies);
    }

    private function threadId()
    {
        if ($this->thread) {
            return $this->thread->id;
        } elseif ($this->repliableIdInAttributes()) {
            return $this->getRepliableId();
        }
        return factory(Thread::class)->create()->id;
    }

    public function toThread($thread)
    {
        $this->thread = $thread;
        return $this;
    }

}