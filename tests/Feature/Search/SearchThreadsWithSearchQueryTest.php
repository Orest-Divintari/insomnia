<?php

namespace Tests\Feature\Search;

use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchThreadsTest;

class SearchThreadsWithSearchQueryTest extends SearchThreadsTest
{
    use RefreshDatabase;

    /** @test */
    public function search_threads_given_a_search_term()
    {
        $user = create(User::class);
        $undesiredThread = create(Thread::class);
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();

        $results = $this->search([
            'type' => 'thread',
            'posted_by' => $user->name,
            'q' => $this->searchTerm,
        ],
            $this->numberOfDesiredThreads
        );

        $this->assertCount(
            $this->numberOfDesiredThreads, $results
        );
        $this->assertContainsThread($results, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_replies_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = create(Thread::class);
        $user = create(User::class);
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->searchTerm)
            ->toThread($desiredThread)
            ->create();

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
        ],
            $this->numberOfDesiredReplies
        );

        $this->assertCount($this->numberOfDesiredReplies, $results);
        $this->assertContainsThreadReply($results, $desiredReply);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_threads_and_replies_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $desiredReply = ReplyFactory::withBody($this->searchTerm)
            ->toThread($desiredThread)
            ->create();

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_threads_and_replies_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $desiredReply = ReplyFactory::withBody($this->searchTerm)
            ->toThread($desiredThread)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $anotherUndesiredReply = ReplyFactory::withBody($this->searchTerm)
            ->toThread($anotherUndesiredThread)
            ->create();
        Carbon::setTestNow();

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
            'last_created' => $daysAgo,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
    }

    /** @test */
    public function search_threads_given_multiple_usernames_and_a_search_term()
    {
        $undesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withBody($this->searchTerm)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withBody($this->searchTerm)
            ->create();
        $numberOfDesiredItems = 2;
        $usernames = "{$john->name}, {$doe->name}";

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsThread($results, $threadByJohn);
        $this->assertContainsThread($results, $threadByDoe);

        $undesiredThread->delete();
        $threadByDoe->delete();
        $threadByJohn->delete();
        $john->delete();
        $doe->delete();
    }

    /** @test */
    public function search_thread_replies_given_multiple_usernames_and_a_search_term()
    {
        $thread = create(Thread::class);
        $undesiredThreadReply = ReplyFactory::withBody($this->searchTerm)
            ->toThread($thread)
            ->create();
        $john = create(User::class);
        $doe = create(User::class);
        $thread = create(Thread::class);
        $threadReplyByJohn = ReplyFactory::by($john)
            ->withBody($this->searchTerm)
            ->toThread($thread)
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->withBody($this->searchTerm)
            ->toThread($thread)
            ->create();
        $numberOfDesiredItems = 2;
        $usernames = "{$john->name}, {$doe->name}";

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $threadReplyByJohn);
        $this->assertContainsThreadReply($results, $threadReplyByDoe);

        $thread->delete();
        $john->delete();
        $doe->delete();
    }

    /** @test */
    public function search_threads_and_replies_given_multiple_usernames_and_search_term()
    {
        $undesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $undesiredThreadReply = ReplyFactory::withBody($this->searchTerm)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withBody($this->searchTerm)
            ->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->withBody($this->searchTerm)
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withBody($this->searchTerm)
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->withBody($this->searchTerm)
            ->toThread($threadByDoe)
            ->create();
        $numberOfDesiredItems = 4;
        $usernames = "{$john->name}, {$doe->name}";

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
            'posted_by' => $usernames,
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
    public function search_threads_and_replies_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->searchTerm)
            ->toThread($desiredThread)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $thirdUndesiredThread = ThreadFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $anotherUndesiredReply = ReplyFactory::withBody($this->searchTerm)
            ->toThread($anotherUndesiredThread)
            ->create();
        $thirdUndesiredReply = ReplyFactory::by($user)
            ->withBody($this->searchTerm)
            ->toThread($anotherUndesiredThread)
            ->create();
        Carbon::setTestNow();

        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);

        $desiredThread->delete();
        $undesiredThread->delete();
        $anotherUndesiredThread->delete();
        $thirdUndesiredThread->delete();
    }

}