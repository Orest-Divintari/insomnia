<?php

namespace Tests\Unit\Statistics;

use App\Models\Thread;
use App\Statistics\ThreadStatistics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected $threadStatistics;

    public function setUp(): void
    {
        parent::setUp();
        app()->instance(ThreadStatistics::class, new FakeThreadStatistics);
        $this->threadStatistics = app(ThreadStatistics::class);
    }

    /** @test */
    public function it_returns_the_threads_count()
    {
        $this->assertEquals(0, $this->threadStatistics->count());

        create(Thread::class);

        $this->assertEquals(1, $this->threadStatistics->count());

        $this->threadStatistics->resetCount();
    }

    /** @test */
    public function it_increments_the_threads_count_when_a_new_thread_is_created()
    {
        $this->assertEmpty(Thread::all());
        create(Thread::class);

        $this->assertEquals(1, $this->threadStatistics->count());

        $this->threadStatistics->resetCount();
    }

    /** @test */
    public function it_decrements_the_threads_count_when_a_thread_is_deleted()
    {
        $this->assertEmpty(Thread::all());
        $thread = create(Thread::class);
        $this->assertEquals(1, $this->threadStatistics->count());

        $thread->delete();

        $this->assertEquals(0, $this->threadStatistics->count());
    }

    /** @test */
    public function it_resets_the_threads_count()
    {
        $this->assertEmpty(Thread::all());
        $thread = create(Thread::class);
        $anotherThread = create(Thread::class);
        $this->assertEquals(2, $this->threadStatistics->count());

        $this->threadStatistics->resetCount();

        $this->assertEquals(0, $this->threadStatistics->count());
    }
}