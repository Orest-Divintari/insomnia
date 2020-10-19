<?php

namespace Tests\Feature;

use App\Thread;
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