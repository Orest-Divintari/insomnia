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
        $this->assertContainsThread($results, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function get_the_threads_that_are_created_by_multiple_users()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();
        $threadByDoe = ThreadFactory::by($doe)->create();
        $numberOfDesiredThreads = 2;
        $undesiredThread = create(Thread::class);
        $usernames = "{$john->name}, {$doe->name}";

        $results = $this->search([
            'type' => 'thread',
            'postedBy' => $usernames,
        ],
            $numberOfDesiredThreads
        );

        $this->assertCount($numberOfDesiredThreads, $results);
        $this->assertContainsThread($results, $threadByDoe);
        $this->assertContainsThread($results, $threadByJohn);

        $threadByDoe->delete();
        $threadByJohn->delete();
        $undesiredThread->delete();
        $john->delete();
        $doe->delete();
    }

    /** @test */
    public function search_the_thread_replies_that_multiple_users_have_posted()
    {
        $undesiredThread = create(Thread::class);
        ReplyFactory::toThread($undesiredThread)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $thread = create(Thread::class);
        $threadReplyByJohn = ReplyFactory::by($john)
            ->toThread($thread)
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->toThread($thread)
            ->create();
        $usernames = "{$john->name}, {$doe->name}";
        $numberOfDesiredReplies = 2;

        $results = $this->search([
            'type' => 'thread',
            'postedBy' => $usernames,
        ],
            $numberOfDesiredReplies
        );

        $this->assertCount($numberOfDesiredReplies, $results);
        $this->assertContainsThreadReply($results, $threadReplyByDoe);
        $this->assertContainsThreadReply($results, $threadReplyByJohn);

        $thread->delete();
        $undesiredThread->delete();
        $john->delete();
        $doe->delete();
    }

    /** @test */
    public function get_the_threads_and_replies_that_are_posted_by_multiple_users()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->toThread($threadByDoe)
            ->create();
        $usernames = "{$john->name},{$doe->name}";
        $numberOfDesiredItems = 4;

        $results = $this->search([
            'type' => 'thread',
            'postedBy' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsThread($results, $threadByJohn);
        $this->assertContainsThreadReply($results, $threadReplyByJohn);
        $this->assertContainsThread($results, $threadByDoe);
        $this->assertContainsThreadReply($results, $threadReplyByDoe);

        $undesiredThread->delete();
        $threadByDoe->delete();
        $threadByJohn->delete();
        $john->delete();
        $doe->delete();
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