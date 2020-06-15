<?php

namespace Tests\Unit;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{

    use RefreshDatabase;
    /** @test */
    public function a_reply_belongs_to_a_thread()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['repliable_id' => $thread->id, 'repliable_type' => Thread::class]);

        $this->assertInstanceOf(Thread::class, $reply->repliable);
    }

    /** @test */
    public function a_reply_belongs_to_the_user_who_posted_it()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $reply = create(Reply::class, [
            'user_id' => $user->id,
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->assertInstanceOf(User::class, $reply->fresh()->poster);
        $this->assertEquals($user->id, $reply->poster->id);

    }
}