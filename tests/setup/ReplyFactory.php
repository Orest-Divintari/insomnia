<?php

namespace Tests\Setup;

use App\Models\Reply;
use App\Models\Thread;
use Tests\Setup\PostFactory;

class ReplyFactory extends PostFactory
{
    protected $thread;
    protected $replies = [];

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $reply = Reply::factory()->create(
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

            $reply = Reply::factory()->create(
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
        return Thread::factory()->create()->id;
    }

    public function toThread($thread)
    {
        $this->thread = $thread;
        return $this;
    }

}