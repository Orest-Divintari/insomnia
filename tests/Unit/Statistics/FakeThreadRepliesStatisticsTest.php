<?php

namespace Tests\Unit\Statistics;

use App\Models\Reply;
use App\Statistics\ThreadReplyStatistics;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FakeThreadRepliesStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected $threadReplyStatistics;

    public function setUp(): void
    {
        parent::setUp();
        app()->instance(ThreadReplyStatistics::class, new FakeThreadReplyStatistics);
        $this->threadReplyStatistics = app(ThreadReplyStatistics::class);
    }

    /** @test */
    public function it_returns_the_thread_replies_count()
    {
        $this->assertEquals(0, $this->threadReplyStatistics->count());

        ReplyFactory::create();

        $this->assertEquals(1, $this->threadReplyStatistics->count());

        $this->threadReplyStatistics->resetCount();
    }

    /** @test */
    public function it_increments_the_thread_replies_count_when_a_new_thread_reply_is_created()
    {
        $this->assertEmpty(Reply::all());
        ReplyFactory::create();

        $this->assertEquals(1, $this->threadReplyStatistics->count());

        $this->threadReplyStatistics->resetCount();
    }

    /** @test */
    public function it_decrements_the_thread_replies_count_when_a_thread_reply_is_deleted()
    {
        $this->assertEmpty(Reply::all());
        $reply = ReplyFactory::create();
        $this->assertEquals(1, $this->threadReplyStatistics->count());

        $reply->delete();

        $this->assertEquals(0, $this->threadReplyStatistics->count());
    }

    /** @test */
    public function it_resets_the_thread_replies_count()
    {
        $this->assertEmpty(Reply::all());
        $reply = ReplyFactory::create();
        $anotherReply = ReplyFactory::create();
        $this->assertEquals(2, $this->threadReplyStatistics->count());

        $this->threadReplyStatistics->resetCount();

        $this->assertEquals(0, $this->threadReplyStatistics->count());
    }
}