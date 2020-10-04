<?php

namespace Tests\Unit;

use App\Filters\ThreadFilters;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $newThreads = $this->threadFilters->builder()->get();

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
        $newPosts = $this->threadFilters->builder()->get();

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
        $contributedThread = $this->threadFilters->builder()->get();

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
        $trendingThreads = $this->threadFilters->builder()->get();

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
        $unansweredThreads = $this->threadFilters->builder()->get();

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
        $subscribedThreads = $this->threadFilters->builder()->get();

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
        $threads = $this->threadFilters->builder()->get();

        $this->assertCount(1, $threads);
        $this->assertEquals($desiredThread->id, $threads[0]->id);
    }

    /** @test */
    public function get_the_threads_that_are_posted_by_a_given_user()
    {
        create(Thread::class);
        $user = create(User::class);
        $desiredThread = create(Thread::class, ['user_id' => $user->id]);

        $this->threadFilters->postedBy($user->name);
        $threads = $this->threadFilters->builder()->get();

        $this->assertCount(1, $threads);
        $this->assertEquals($desiredThread->id, $threads[0]->id);
    }

    /** @test */
    public function throw_error_if_the_given_username_does_not_exist()
    {
        create(Thread::class);
        $user = create(User::class);
        $desiredThread = create(Thread::class, ['user_id' => $user->id]);

        $randomName = 'asdf';
        $this->expectException(ModelNotFoundException::class);
        $this->threadFilters->postedBy($randomName);
    }

    /** @test */
    public function get_the_threads_that_were_created_a_given_number_of_days_ago()
    {
        $olderThread = create(
            Thread::class,
            ['created_at' => Carbon::now()->subDays(10)]
        );
        $numberOfDesiredThreads = 2;
        $daysAgo = 5;
        $desiredThreads = createMany(
            Thread::class,
            2,
            ['created_at' => Carbon::now()->subDays($daysAgo)]
        );

        $this->threadFilters->lastCreated($daysAgo);
        $threads = $this->threadFilters->builder()->get();

        $this->assertCount($numberOfDesiredThreads, $threads);
        $this->assertTrue(
            $threads->containsStrict(
                'id',
                $desiredThreads[0]->id)
        );
        $this->assertTrue(
            $threads->containsStrict(
                'id',
                $desiredThreads[1]->id
            )
        );
    }

}