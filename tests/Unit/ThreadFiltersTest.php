<?php

namespace Tests\Unit;

use App\Filters\ThreadFilters;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadFiltersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The instance of thread filters
     *
     * @var ThreadFilters
     */
    protected $threadFilters;

    public function setUp(): void
    {
        parent::setUp();
        $this->threadFilters = app(
            ThreadFilters::class,
            ['builder' => Thread::query()]
        );
    }
    /** @test */
    public function sort_threads_by_creation_date()
    {
        $oldThread = create(
            Thread::class,
            ['created_at' => Carbon::now()->subDays(5)]
        );
        $newThread = create(Thread::class);

        $this->threadFilters->newThreads();
        $newThreads = $this->threadFilters->getBuilder()->get();

        $this->assertEquals(
            $newThread->id,
            $newThreads->first()->id
        );
    }

    /** @test */
    public function get_threads_with_the_most_recent_replies()
    {
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = create(Thread::class);
        ReplyFactory::create(
            [
                'repliable_id' => $inactiveThread->id,
            ]);
        Carbon::setTestNow(Carbon::now()->addDay());

        $recentlyActiveThread = create(Thread::class);
        ReplyFactory::create(['repliable_id' => $recentlyActiveThread->id]);

        $this->threadFilters->newPosts();
        $newPosts = $this->threadFilters->getBuilder()->get();

        $this->assertEquals(
            $recentlyActiveThread->id,
            $newPosts->first()->id
        );
    }

    /** @test */
    public function get_the_threads_that_a_user_has_contributed_to()
    {
        $user = create(User::class);
        $reply = ReplyFactory::create(['user_id' => $user->id]);
        ReplyFactory::create();

        $this->threadFilters->contributed($user->name);
        $contributedThread = $this->threadFilters->getBuilder()->get();

        $this->assertCount(1, $contributedThread);
        $this->assertEquals($reply->repliable_id, $contributedThread[0]->id);
    }

    /** @test */
    public function get_the_trending_threads()
    {
        $mostPopularThread = create(Thread::class, ['views' => 10, 'replies_count' => 10]);
        $secondMostPopularThread = create(Thread::class, ['views' => 15, 'replies_count' => 5]);
        $thirdMostPopularThread = create(Thread::class, ['views' => 5, 'replies_count' => 5]);

        $this->threadFilters->trending();
        $trendingThreads = $this->threadFilters->getBuilder()->get();

        $this->assertEquals($mostPopularThread->id, $trendingThreads[0]->id);
        $this->assertEquals($secondMostPopularThread->id, $trendingThreads[1]->id);
        $this->assertEquals($thirdMostPopularThread->id, $trendingThreads[2]->id);
    }

    /** @test */
    public function get_threads_that_have_no_replies()
    {
        ReplyFactory::createMany(5);
        $unansweredThread = create(Thread::class);

        $this->threadFilters->unanswered();
        $unansweredThreads = $this->threadFilters->getBuilder()->get();

        $this->assertCount(1, $unansweredThreads);
        $this->assertEquals($unansweredThread->id, $unansweredThreads[0]->id);
    }

    /** @test */
    public function get_the_threads_that_the_authenticated_user_has_subscribed_to()
    {
        $threads = createMany(Thread::class, 5);
        $subscribedThread = create(Thread::class);

        $user = $this->signIn();
        $subscribedThread->subscribe($user->id);

        $this->threadFilters->watched();
        $subscribedThreads = $this->threadFilters->getBuilder()->get();

        $this->assertCount(1, $subscribedThreads);
        $this->assertEquals($subscribedThread->id, $subscribedThreads[0]->id);
    }

    /** @test */
    public function get_the_threads_that_were_last_updated_x_days_ago()
    {
        $daysAgo = 2;
        $desiredThread = create(Thread::class, ['updated_at' => Carbon::now()->subDays($daysAgo)]);
        createMany(Thread::class, 3, ['updated_at' => Carbon::now()->subDays(5)]);

        $this->threadFilters->lastUpdated($daysAgo);
        $threads = $this->threadFilters->getBuilder()->get();

        $this->assertCount(1, $threads);
        $this->assertEquals($desiredThread->id, $threads[0]->id);
    }

}