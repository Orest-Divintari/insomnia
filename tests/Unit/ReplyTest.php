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
        $reply = create('App\Reply');
        $this->assertInstanceOf(Thread::class, $reply->thread);
    }
}