<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'algolia']);
    }

    /** @test */
    public function a_user_can_get_threads_by_username()
    {
        $uric = create(User::class, ['name' => 'uric']);
        $this->signIn($uric);
        $threadByUric = create(Thread::class, ['user_id' => $uric->id]);

        $anotherUser = $this->signIn();
        $otherThreads = createMany(Thread::class, 5);

        do {
            $results = $this->getJson(
                route(
                    'search.show',
                    ['type' => 'thread', 'postedBy' => $uric->name]
                )
            )->json()['data'];
        } while (empty($results));

        $this->assertCount(1, $results);

        $threadByUric->delete();
        $otherThreads->each->delete();
    }

    /** @test */
    public function a_user_can_search_threads_in_combination_with_a_given_user_name()
    {
        $searchTerm = 'yo';

        $uric = create(User::class, ['name' => 'uric']);
        $this->signIn($uric);
        $threadByUric = create(Thread::class, [
            'user_id' => $uric->id,
            'body' => $searchTerm,
        ]);

        $anotherUser = $this->signIn();
        $otherThreads = createMany(Thread::class, 5);

        do {
            $results = $this->getJson(
                route(
                    'search.show',
                    [
                        'type' => 'thread',
                        'postedBy' => $uric->name,
                        'q' => $searchTerm,
                    ]
                )
            )->json()['data'];
        } while (empty($results));

        $this->assertCount(1, $results);

        $threadByUric->delete();
        $otherThreads->each->delete();
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
    public function a_user_can_get_threads_and_replies_by_creation_date()
    {
        $this->signIn();
        $numOfDesiredThreads = 2;
        $numOfDesiredReplies = 5;
        $numberOfDesiredItems = $numOfDesiredThreads + $numOfDesiredReplies;
        $numOfUndesiredThreads = 5;

        Carbon::setTestNow(Carbon::now()->subDays(5));
        $desiredThreads = createMany(
            Thread::class,
            $numOfDesiredThreads
        );
        ReplyFactory::createMany(
            $numOfDesiredReplies,
            ['repliable_id' => $desiredThreads->first()->id]
        );

        Carbon::setTestNow(Carbon::now()->addDays(5));
        Carbon::setTestNow(Carbon::now()->subDays(10));

        $undesiredThreads = createMany(
            Thread::class,
            $numOfUndesiredThreads,
        );
        ReplyFactory::createMany(
            5,
            ['repliable_id' => $undesiredThreads->first()->id]
        );

        Carbon::setTestNow(Carbon::now()->addDays(10));
        do {
            $results = $this->getJson(route('search.show', ['type' => 'thread', 'lastCreated' => 5]))
                ->json()['data'];
        } while (empty($results));

        $this->assertCount($numberOfDesiredItems, $results);

        $desiredThreads->each->replies->each->delete();
        $desiredThreads->each->delete();
        $undesiredThreads->each->replies->each->delete();
        $undesiredThreads->each->delete();
    }

    /** @test */
    public function a_user_can_search_threads_and_replies_in_combination_with_creation_date()
    {
        $searchTerm = 'yo';

        $this->signIn();
        $numOfDesiredThreads = 2;
        $numOfDesiredReplies = 5;
        $numberOfDesiredItems = $numOfDesiredThreads + $numOfDesiredReplies;
        $numOfUndesiredThreads = 5;

        Carbon::setTestNow(Carbon::now()->subDays(5));
        $desiredThreads = createMany(
            Thread::class,
            $numOfDesiredThreads,
            ['body' => $searchTerm]
        );
        ReplyFactory::createMany(
            $numOfDesiredReplies,
            [
                'body' => $searchTerm,
                'repliable_id' => $desiredThreads->first()->id,
            ],

        );

        Carbon::setTestNow(Carbon::now()->addDays(5));
        Carbon::setTestNow(Carbon::now()->subDays(10));

        $undesiredThreads = createMany(
            Thread::class,
            $numOfUndesiredThreads,
            ['body' => $searchTerm]
        );
        ReplyFactory::createMany(
            5,
            [
                'body' => $searchTerm,
                'repliable_id' => $undesiredThreads->first()->id,
            ]
        );

        Carbon::setTestNow(Carbon::now()->addDays(10));
        do {
            $results = $this->getJson(route('search.show', ['q' => $searchTerm, 'type' => 'thread', 'lastCreated' => 5]))
                ->json()['data'];
        } while (empty($results));

        $this->assertCount($numberOfDesiredItems, $results);

        $desiredThreads->each->replies->each->delete();
        $desiredThreads->each->delete();
        $undesiredThreads->each->replies->each->delete();
        $undesiredThreads->each->delete();

    }

    /** @test */
    public function a_user_can_search_threads_by_username_and_creation_date()
    {
        $searchTerm = 'yo';
        $daysAgo = 5;
        $numberOfReplies = 5;
        $numberOfDesiredThreads = 1;

        $uric = $this->signIn();
        $numberOfDesiredItems = $numberOfReplies + $numberOfDesiredThreads;

        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));

        $threadByUric = create(Thread::class, [
            'user_id' => $uric->id,
            'body' => $searchTerm,
        ]);
        ReplyFactory::createMany(
            $numberOfReplies,
            [
                'body' => $searchTerm,
                'user_id' => $uric->id,
                'repliable_id' => $threadByUric->id,
            ]);

        $anotherUser = $this->signIn();
        $otherThread = create(Thread::class);
        ReplyFactory::createMany(
            $numberOfReplies,
            [
                'body' => $searchTerm,
                'repliable_id' => $otherThread->id,
            ]);

        do {
            $results = $this->getJson(
                route(
                    'search.show',
                    [
                        'type' => 'thread',
                        'postedBy' => $uric->name,
                        'lastCreated' => $daysAgo,
                        'q' => $searchTerm,
                    ]
                )
            )->json()['data'];
        } while (empty($results));

        $this->assertCount($numberOfDesiredItems, $results);

        $threadByUric->replies->each->delete();
        $threadByUric->delete();

        $otherThread->replies->each->delete();
        $otherThread->delete();
    }

    /** @test */
    // public function tsek()
    // {
    //     $thread = create(Thread::class, ['body' => 'one', 'title' => 'two']);
    //     create(Thread::class, ['title' => 'two']);

    //     dd(Thread::search('one')->with([
    //         'facets' => ['*'],
    //         'facetFilters' => 'title:two',
    //     ])->get()->toArray());
    // }

}