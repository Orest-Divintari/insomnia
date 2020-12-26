<?php

namespace Tests\Feature\Search;

use App\Thread;
use Carbon\Carbon;
use Tests\Feature\Search\SearchThreadsTest;

class SearchThreadTitlesWithSearchQueryTest extends SearchThreadsTest
{

    /** @test */
    public function search_threads_title_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $user = $this->signIn();
        $desiredThread = create(Thread::class, [
            'user_id' => $user->id,
            'title' => $this->searchTerm,
        ]);

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
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(
            Thread::class,
            ['title' => $this->searchTerm]
        );
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $anotherUndesiredThread = create(
            Thread::class,
            ['title' => $this->searchTerm]
        );
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));

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
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(
            Thread::class,
            [
                'title' => $this->searchTerm,
                'user_id' => $user->id,
            ]
        );

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $anotherUndesiredThread = create(
            Thread::class,
            ['title' => $this->searchTerm]
        );
        $thirdUndesiredThread = create(
            Thread::class,
            [
                'title' => $this->searchTerm,
                'user_id' => $user->id,
            ]
        );
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));

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