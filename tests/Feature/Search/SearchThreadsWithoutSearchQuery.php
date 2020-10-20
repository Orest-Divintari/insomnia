<?php

namespace Tests\Feature\Search;

use App\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchThreadsTest;

class SearchThreadsWithoutSearchQuery extends SearchThreadsTest
{
    use RefreshDatabase;

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

        $results = $this->search([
            'type' => 'thread',
            'postedBy' => $user->name,
        ],
            $this->numberOfDesiredThreads
        );

        $this->assertCount(
            $this->numberOfDesiredThreads,
            $results
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

        $results = $this->search([
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

        $results = $this->search([
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
        $results = $this->search([
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
        $results = $this->search([
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
    public function get_the_threads_and_replies_that_were_created_the_last_given_number_of_days_by_a_given_username()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );
        $desiredReply = ReplyFactory::create([
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
        ]);

        $anotherUser = $this->signIn();
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );
        $anotherUndesiredReply = ReplyFactory::create([
            'repliable_id' => $anotherUndesiredThread->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'thread',
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
    }

}