<?php

namespace Tests\Feature\Search;

use App\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchThreadsTest;

class SearchThreadsWithSearchQueryTest extends SearchThreadsTest
{
    use RefreshDatabase;

    /** @test */
    public function search_threads_given_a_search_term()
    {
        $this->withoutExceptionHandling();
        $undesiredThread = create(Thread::class);

        $user = $this->signIn();
        $desiredThread = create(Thread::class, [
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        $results = $this->search([
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

        $results = $this->search([
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

        $results = $this->search([
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
        $results = $this->search([
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
        $results = $this->search([
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

}