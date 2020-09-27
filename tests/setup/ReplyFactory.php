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

        $thread = $this->createThread($attributes);

        $reply = factory(Reply::class)->create(
            array_merge(
                [
                    'user_id' => $this->user->id,
                    'repliable_id' => $thread->id,
                    'repliable_type' => Thread::class,
                    'position' => 2,
                ],
                $attributes
            ));

        return $reply;
    }

    public function createMany($count = 1, $attributes = [])
    {

        $this->user = $this->user ?: factory(User::class)->create();

        $thread = $this->createThread($attributes);

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
            $thread->increment('replies_count');

        }
        return collect($replies);

    }

    public function createThread($attributes)
    {
        if (array_key_exists('repliable_id', $attributes)) {
            $thread = Thread::find($attributes['repliable_id']);
        } else {
            $thread = create(Thread::class);
        }
        return $thread;
    }
    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}