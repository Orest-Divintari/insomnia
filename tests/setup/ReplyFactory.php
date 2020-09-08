<?php

namespace Tests\Setup;

use App\Reply;
use App\Thread;
use App\User;

class ReplyFactory
{
    protected $user;

    public function create()
    {
        $this->user = $this->user ?: factory(User::class)->create();

        $thread = factory(Thread::class)->create();

        $reply = factory(Reply::class)->create([
            'user_id' => $this->user->id,
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        return $reply;
    }

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}