<?php

namespace Tests\Feature;

use App\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\SearchThreadsTest;

class AlgoliaSearchThreadTitlesTest extends SearchThreadsTest
{
    use RefreshDatabase;

    protected $numberOfDesiredProfilePosts;
    protected $numberOfDesiredComments;
    protected $numberOfUndesiredComments;
    protected $numberOfUndesiredProfilePosts;
    protected $totalNumberOfDesiredItems;
    protected $totalNumberOfUndesiredItems;

    public function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'algolia']);
        $this->numberOfDesiredProfilePosts = 1;
        $this->numberOfUndesiredProfilePosts = 1;
        $this->numberOfDesiredComments = 1;
        $this->numberOfUndesiredComments = 1;
        $this->totalNumberOfDesiredItems = $this->numberOfDesiredComments + $this->numberOfDesiredProfilePosts;
        $this->totalNumberOfUndesiredItems = $this->numberOfUndesiredComments + $this->numberOfUndesiredProfilePosts;
    }

    /** @test */
    public function search_threads_title_given_a_search_term()
    {
        $this->withoutExceptionHandling();

        $undesiredThread = create(Thread::class);

        $user = $this->signIn();
        $desiredThread = create(Thread::class, [
            'user_id' => $user->id,
            'title' => $this->searchTerm,
        ]);

        $results = $this->search([
            'type' => 'thread',
            'only_title' => true,
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
    public function search_threads_title_given_a_search_term_that_were_created_a_given_number_of_days_ago()
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
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = create(
            Thread::class,
            ['title' => $this->searchTerm]
        );

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'thread',
            'q' => $this->searchTerm,
            'only_title' => true,
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
    public function a_user_can_search_threads_and_replies_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
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
        $undesiredThread = create(Thread::class);
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
            'type' => 'thread',
            'q' => $this->searchTerm,
            'only_title' => true,
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