<?php

namespace Tests\Setup;

use App\Reply;
use App\Thread;
use App\User;

class ReplyFactory
{
    protected $user;

    public function create($attributes = [])
    {

        $this->user = $this->user ?: factory(User::class)->create();

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->create(
            array_merge(
                [
                    'user_id' => $this->user->id,
                    'repliable_id' => $thread->id,
                    'repliable_type' => Thread::class,
                    'position' => 1,
                ],
                $attributes
            ));

        return $reply;
    }

    public function createMany($count = 1, $attributes = [])
    {

        $this->user = $this->user ?: factory(User::class)->create();

        $thread = factory(Thread::class)->create();

        for ($repliesCounter = 1; $repliesCounter <= $count; $repliesCounter++) {

            $replies[] = factory(Reply::class)->create(
                array_merge(
                    [
                        'user_id' => $this->user->id,
                        'repliable_id' => $thread->id,
                        'repliable_type' => Thread::class,
                        'position' => $repliesCounter + 1,
                    ],
                    $attributes
                ));
        }
        return collect($replies);

    }

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}