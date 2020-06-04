<?php

namespace Tests\Unit;

use App\Thread;
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
}