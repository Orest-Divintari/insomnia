<?php

namespace Tests\Feature\Search;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SearchableTest;

class SearchThreadsWithSearchQueryTest extends TestCase
{
    use RefreshDatabase, SearchableTest;

    /** @test */
    public function search_threads_given_a_search_term()
    {
        $user = create(User::class);
        $undesiredThread = create(Thread::class);
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $numberOfDesiredThreads = 1;

        $results = $this->searchJson([
            'type' => 'thread',
            'posted_by' => $user->name,
            'q' => $this->searchTerm(),
        ],
            $numberOfDesiredThreads
        );

        $this->assertCount(
            $numberOfDesiredThreads, $results
        );
        $this->assertContainsThread($results, $desiredThread);

        $this->emptyIndices();
    }

    /** @test */
    public function search_replies_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = create(Thread::class);
        $user = create(User::class);
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->sentence())
            ->toThread($desiredThread)
            ->create();
        $numberOfDesiredReplies = 1;

        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
        ],
            $numberOfDesiredReplies
        );

        $this->assertCount($numberOfDesiredReplies, $results);
        $this->assertContainsThreadReply($results, $desiredReply);

        $this->emptyIndices();
    }

    /** @test */
    public function search_threads_and_replies_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = ThreadFactory::withBody($this->sentence())->create();
        $desiredReply = ReplyFactory::withBody($this->sentence())
            ->toThread($desiredThread)
            ->create();
        $totalNumberOfDesiredItems = 2;
        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);

        $this->emptyIndices();
    }

    /** @test */
    public function search_threads_and_replies_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::withBody($this->sentence())->create();
        $desiredReply = ReplyFactory::withBody($this->sentence())
            ->toThread($desiredThread)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = ThreadFactory::withBody($this->sentence())->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $anotherUndesiredReply = ReplyFactory::withBody($this->sentence())
            ->toThread($anotherUndesiredThread)
            ->create();
        Carbon::setTestNow();
        $totalNumberOfDesiredItems = 2;

        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
            'last_created' => $daysAgo,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);

        $this->emptyIndices();
    }

    /** @test */
    public function search_threads_given_multiple_usernames_and_a_search_term()
    {
        $undesiredThread = ThreadFactory::withBody($this->sentence())
            ->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withBody($this->sentence())
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withBody($this->sentence())
            ->create();
        $numberOfDesiredItems = 2;
        $usernames = "{$john->name}, {$doe->name}";

        $threads = Thread::boolSearch()
            ->join(Reply::class)
            ->should('wildcard', ['title' => '*'])
            ->should('wildcard', ['body' => '*'])
            ->execute()
            ->models()
            ->toArray();

        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsThread($results, $threadByJohn);
        $this->assertContainsThread($results, $threadByDoe);

        $this->emptyIndices();
    }

    /** @test */
    public function search_thread_replies_given_multiple_usernames_and_a_search_term()
    {
        $thread = create(Thread::class);
        $undesiredThreadReply = ReplyFactory::withBody($this->sentence())
            ->toThread($thread)
            ->create();
        $john = create(User::class);
        $doe = create(User::class);
        $thread = create(Thread::class);
        $threadReplyByJohn = ReplyFactory::by($john)
            ->withBody($this->sentence())
            ->toThread($thread)
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->withBody($this->sentence())
            ->toThread($thread)
            ->create();
        $numberOfDesiredItems = 2;
        $usernames = "{$john->name}, {$doe->name}";

        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $threadReplyByJohn);
        $this->assertContainsThreadReply($results, $threadReplyByDoe);

        $this->emptyIndices();
    }

    /** @test */
    public function search_threads_and_replies_given_multiple_usernames_and_search_term()
    {
        $undesiredThread = ThreadFactory::withBody($this->sentence())->create();
        $undesiredThreadReply = ReplyFactory::withBody($this->sentence())->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withBody($this->sentence())
            ->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->withBody($this->sentence())
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withBody($this->sentence())
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->withBody($this->sentence())
            ->toThread($threadByDoe)
            ->create();
        $numberOfDesiredItems = 4;
        $usernames = "{$john->name}, {$doe->name}";

        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
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

        $this->emptyIndices();
    }

    /** @test */
    public function search_threads_and_replies_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->sentence())
            ->toThread($desiredThread)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = ThreadFactory::withBody($this->sentence())->create();
        $thirdUndesiredThread = ThreadFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $anotherUndesiredReply = ReplyFactory::withBody($this->sentence())
            ->toThread($anotherUndesiredThread)
            ->create();
        $thirdUndesiredReply = ReplyFactory::by($user)
            ->withBody($this->sentence())
            ->toThread($anotherUndesiredThread)
            ->create();
        Carbon::setTestNow();
        $totalNumberOfDesiredItems = 2;
        $results = $this->searchJson([
            'type' => 'thread',
            'q' => $this->searchTerm(),
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems, $results
        );
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);

        $this->emptyIndices();
    }

}