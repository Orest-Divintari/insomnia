<?php

namespace Tests\Feature\Search;

use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ThreadFactory;
use Tests\Feature\Search\SearchThreadsTest;

class SearchThreadTitlesWithSearchQueryTest extends SearchThreadsTest
{

    /** @test */
    public function search_threads_title_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $user = create(User::class);
        $desiredThread = ThreadFactory::by($user)
            ->withTitle($this->searchTerm)
            ->create();

        $results = $this->search([
            'onlyTitle' => true,
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
        $anotherUndesiredThread->delete();
    }

    /** @test */
    public function search_threads_title_that_were_created_a_given_number_of_days_ago_given_a_search_term_()
    {
        $user = create(User::class);
        $daysAgo = 5;
        $desiredThread = ThreadFactory::by($user)
            ->withTitle($this->searchTerm)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $anotherUndesiredThread = ThreadFactory::withTitle($this->searchTerm)->create();
        Carbon::setTestNow();

        $results = $this->search([
            'q' => $this->searchTerm,
            'onlyTitle' => true,
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
        $anotherUndesiredThread->delete();
    }

    /** @test */
    public function a_user_can_search_threads_title_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
    {
        $user = create(User::class);
        $daysAgo = 5;
        $desiredThread = ThreadFactory::by($user)
            ->withTitle($this->searchTerm)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $anotherUndesiredThread = ThreadFactory::withTitle($this->searchTerm)->create();
        $thirdUndesiredThread = ThreadFactory::by($user)
            ->withTitle($this->searchTerm)
            ->create();
        Carbon::setTestNow();

        $results = $this->search([
            'q' => $this->searchTerm,
            'onlyTitle' => true,
            'lastCreated' => $daysAgo,
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
        $anotherUndesiredThread->delete();
    }

}