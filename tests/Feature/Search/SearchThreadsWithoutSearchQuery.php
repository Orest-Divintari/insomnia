<?php

namespace Tests\Feature\Search;

use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchThreadsTest;

class SearchThreadsWithoutSearchQuery extends SearchThreadsTest
{
    use RefreshDatabase;

    /** @test */
    public function get_the_threads_that_are_created_by_a_given_username()
    {
        $user = create(User::class);
        $desiredThread = ThreadFactory::by($user)->create();
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
        ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = create(Thread::class);
        $user = create(User::class);
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();

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
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->cr();
        $user = create(User::class);
        $desiredThread = ThreadFactory::by($user)->create();
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();

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
    public function get_the_threads_and_replies_that_were_created_the_last_given_number_of_days_by_a_given_username()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::by($user)->create();
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredThread = ThreadFactory::by($user)->create();
        $anotherUndesiredReply = ReplyFactory::by($user)
            ->toThread($anotherUndesiredThread)
            ->create();
        Carbon::setTestNow();

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
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsThreadReply($results, $desiredReply);

        $desiredThread->delete();
        $undesiredThread->delete();
        $anotherUndesiredThread->delete();
    }

}