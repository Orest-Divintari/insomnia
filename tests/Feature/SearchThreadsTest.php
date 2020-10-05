<?php

namespace Tests\Feature;

use App\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchThreadsTest extends SearchTest
{
    use RefreshDatabase;

    protected $numberOfDesiredThreads;
    protected $numberOfDesiredReplies;
    protected $numberOfUndesiredReplies;
    protected $numberOfUndesiredThreads;
    protected $totalNumberOfDesiredItems;
    protected $totalNumberOfUndesiredItems;

    public function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'algolia']);
        $this->numberOfDesiredThreads = 1;
        $this->numberOfUndesiredThreads = 1;
        $this->numberOfDesiredReplies = 1;
        $this->numberOfUndesiredReplies = 1;
        $this->totalNumberOfDesiredItems = $this->numberOfDesiredReplies + $this->numberOfDesiredThreads;
        $this->totalNumberOfUndesiredItems = $this->numberOfUndesiredReplies + $this->numberOfUndesiredThreads;

    }

    /** @test */
    public function get_the_threads_that_are_created_by_a_given_username()
    {
        $user = $this->signIn();
        $desiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );

        $anotherUser = $this->signIn();
        $undesiredThread = create(Thread::class);

        $results = $this->search(
            [
                'type' => 'thread',
                'postedBy' => $user->name,
            ],
            $this->numberOfDesiredThreads
        );

        $this->assertCount(
            $this->numberOfDesiredThreads, $results
        );
        $first = $this->numberOfDesiredThreads - 1;
        $this->assertThread($results[$first], $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function get_the_replies_that_a_given_username_has_posted()
    {
        $undesiredThread = create(Thread::class);
        ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        $desiredThread = create(Thread::class);
        $user = $this->signIn();
        $desiredReply = ReplyFactory::create([
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
        ]);

        $results = $this->search(
            [
                'type' => 'thread',
                'postedBy' => $user->name,
            ],
            $this->numberOfDesiredReplies
        );

        $this->assertCount(
            $this->numberOfDesiredReplies, $results
        );
        $first = $this->numberOfDesiredReplies - 1;
        $this->assertReply(
            $results[$first],
            $desiredReply,
            $desiredThread
        );

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function get_the_threads_and_replies_that_are_posted_by_a_given_username()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        $user = $this->signIn();
        $desiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );
        $desiredReply = ReplyFactory::create([
            'user_id' => $user->id,
            'repliable_id' => $desiredThread->id,
        ]);

        $results = $this->search(
            [
                'type' => 'thread',
                'postedBy' => $user->name,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );
        $results = collect($results);
        $resultedReply = $results->firstWhere('type', 'thread-reply');
        $resultedThread = $results->firstWhere('type', 'thread');

        $this->assertThread($resultedThread, $desiredThread);
        $this->assertReply($resultedReply, $desiredReply, $desiredThread);
    }

    /** @test */
    public function get_the_threads_that_were_created_that_last_given_number_of_days()
    {
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(Thread::class);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search(
            [
                'type' => 'thread',
                'lastCreated' => $daysAgo,
            ],
            $this->numberOfDesiredThreads
        );

        $this->assertCount(
            $this->numberOfDesiredThreads, $results
        );
        $first = $this->numberOfDesiredThreads - 1;
        $this->assertThread($results[$first], $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }
    /** @test */
    public function a_user_can_get_threads_and_replies_that_were_created_the_last_given_number_of_days()
    {
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(Thread::class);
        $desiredReply = ReplyFactory::create([
            'repliable_id' => $desiredThread->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search(
            [
                'type' => 'thread',
                'lastCreated' => $daysAgo,
            ],
            $this->numberOfDesiredItems
        );

        $this->assertCount(
            $this->numberOfDesiredItems, $results
        );
        $results = collect($results);
        $resultedReply = $results->firstWhere('type', 'thread-reply');
        $resultedThread = $results->firstWhere('type', 'thread');
        $this->assertThread($resultedThread, $desiredThread);
        $this->assertReply($resultedReply, $desiredReply, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_threads_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);

        $user = $this->signIn();
        $desiredThread = create(Thread::class, [
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        $results = $this->search(
            [
                'type' => 'thread',
                'postedBy' => $user->name,
                'q' => $this->searchTerm,
            ],
            $this->numberOfDesiredThreads
        );

        $this->assertCount(
            $this->numberOfDesiredThreads, $results
        );
        $first = $this->numberOfDesiredThreads - 1;
        $this->assertThread($results[$first], $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_replies_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        $desiredThread = create(Thread::class);
        $user = $this->signIn();
        $desiredReply = ReplyFactory::create([
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        $results = $this->search(
            [
                'type' => 'thread',
                'q' => $this->searchTerm,
            ],
            $this->numberOfDesiredReplies
        );

        $this->assertCount(
            $this->numberOfDesiredReplies, $results
        );
        $first = $this->numberOfUndesiredReplies - 1;
        $this->assertReply($results[$first], $desiredReply, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_threads_and_replies_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        $desiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $desiredReply = ReplyFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredThread->id,
        ]);

        $results = $this->search(
            [
                'type' => 'thread',
                'q' => $this->searchTerm,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );

        $results = collect($results);
        $resultedThread = $results->firstWhere('type', 'thread');
        $resultedReply = $results->firstWhere('type', 'thread-reply');

        $this->assertReply($resultedReply, $desiredReply, $desiredThread);
        $this->assertThread($resultedThread, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    // /** @test */
    // public function a_user_can_get_threads_by_minimum_number_of_replies()
    // {
    //     $uric = create(User::class, ['name' => 'uric']);
    //     $this->signIn($uric);

    //     $threadByUric = create(Thread::class, ['user_id' => $uric->id]);
    //     $numberOfReplies = 5;
    //     ReplyFactory::createMany(
    //         $numberOfReplies,
    //         ['repliable_id' => $threadByUric->id]
    //     );

    //     $anotherUser = $this->signIn();
    //     $otherThreads = createMany(Thread::class, 3);

    //     $thread = create(Thread::class);
    //     ReplyFactory::create(['repliable_id' => $thread->id]);

    //     do {
    //         $results = $this->getJson(
    //             route(
    //                 'search.show',
    //                 ['type' => 'thread', 'numberOfReplies' => $numberOfReplies]
    //             )
    //         )->json()['data'];
    //     } while (empty($results));

    //     $this->assertCount(6, $results);

    //     $threadByUric->replies->each->delete();
    //     $threadByUric->delete();
    //     $otherThreads->each->delete();
    // }

    // /** @test */
    // public function a_user_can_search_threads_in_combination_with_a_minimum_number_of_replies()
    // {
    //     $searchTerm = 'yo';

    //     $uric = create(User::class, ['name' => 'uric']);
    //     $this->signIn($uric);

    //     $threadByUric = create(Thread::class, [
    //         'user_id' => $uric->id,
    //         'body' => $searchTerm,
    //     ]);

    //     $numberOfReplies = 5;
    //     ReplyFactory::createMany(
    //         $numberOfReplies,
    //         [
    //             'repliable_id' => $threadByUric->id,
    //             'body' => $searchTerm,
    //         ]
    //     );

    //     $anotherUser = $this->signIn();
    //     $otherThreads = createMany(Thread::class, 3, ['body' => $searchTerm]);

    //     $thread = create(Thread::class);
    //     ReplyFactory::create([
    //         'repliable_id' => $thread->id,
    //         'body' => $searchTerm,
    //     ]);

    //     do {
    //         $results = $this->getJson(
    //             route(
    //                 'search.show',
    //                 [
    //                     'type' => 'thread',
    //                     'numberOfReplies' => $numberOfReplies,
    //                     'q' => $searchTerm,
    //                 ]
    //             )
    //         )->json()['data'];
    //     } while (empty($results));

    //     $this->assertCount(6, $results);

    //     $threadByUric->replies->each->delete();
    //     $threadByUric->delete();
    //     $otherThreads->each->delete();
    // }

    /** @test */
    public function a_user_can_search_threads_and_replies_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $desiredReply = ReplyFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredThread->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);
        $anotherUndesiredReply = ReplyFactory::create([
            'repliable_id' => $anotherUndesiredThread->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search(
            [
                'type' => 'thread',
                'q' => $this->searchTerm,
                'lastCreated' => $daysAgo,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );
        $results = collect($results);
        $resultedReply = $results->firstWhere('type', 'thread-reply');
        $resultedThread = $results->firstWhere('type', 'thread');
        $this->assertThread($resultedThread, $desiredThread);
        $this->assertReply($resultedReply, $desiredReply, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function a_user_can_search_threads_and_replies_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(
            Thread::class,
            [
                'body' => $this->searchTerm,
                'user_id' => $user->id,
            ]
        );
        $desiredReply = ReplyFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $thirdUndesiredThread = create(
            Thread::class,
            [
                'body' => $this->searchTerm,
                'user_id' => $user->id,
            ]
        );
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);
        $anotherUndesiredReply = ReplyFactory::create([
            'repliable_id' => $anotherUndesiredThread->id,
            'body' => $this->searchTerm,
        ]);
        $thirdUndesiredReply = ReplyFactory::create([
            'repliable_id' => $anotherUndesiredThread->id,
            'body' => $this->searchTerm,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search(
            [
                'type' => 'thread',
                'q' => $this->searchTerm,
                'lastCreated' => $daysAgo,
                'postedBy' => $user->name,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );

        $results = collect($results);
        $resultedReply = $results->firstWhere('type', 'thread-reply');
        $resultedThread = $results->firstWhere('type', 'thread');

        $this->assertThread($resultedThread, $desiredThread);
        $this->assertReply($resultedReply, $desiredReply, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
        $anotherUndesiredThread->delete();
        $thirdUndesiredThread->delete();
    }

    /**
     * Assert that the reply is correct and the required relationships are loaded
     *
     * @param array $resultedReply
     * @param Reply $desiredReply
     * @param Thread $desiredThread
     * @return void
     */
    public function assertReply($resultedReply, $desiredReply, $desiredThread)
    {
        $this->assertEquals(
            $resultedReply['id'], $desiredReply->id
        );
        $this->assertEquals(
            $resultedReply['poster']['id'], $desiredReply->poster->id
        );
        $this->assertEquals(
            $resultedReply['repliable']['id'], $desiredThread->id
        );
        $this->assertEquals(
            $resultedReply['repliable']['poster']['id'], $desiredThread->poster->id
        );
        $this->assertEquals(
            $resultedReply['repliable']['category']['id'], $desiredThread->category->id,
        );
    }

    /**
     * Assert that thread is correct and the required relationships are loaded
     *
     * @param array $resultedThread
     * @param Thread $desiredThread
     * @return void
     */
    public function assertThread($resultedThread, $desiredThread)
    {
        $this->assertEquals(
            $resultedThread['id'], $desiredThread->id
        );
        $this->assertEquals(
            $resultedThread['poster']['id'], $desiredThread->poster->id
        );
        $this->assertEquals(
            $resultedThread['category']['id'], $desiredThread->category->id,
        );
    }

}