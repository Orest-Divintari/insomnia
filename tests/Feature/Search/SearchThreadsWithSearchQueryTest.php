<?php

namespace Tests\Feature\Search;

use App\Thread;
use App\User;
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
            'postedBy' => $user->name,
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
            'lastCreated' => $daysAgo,
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
    public function a_user_can_search_threads_and_replies_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
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
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
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