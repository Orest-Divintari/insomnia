<?php

namespace Tests\Feature\Search;

use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SearchableTest;

class SearchThreadTitlesWithSearchQueryTest extends TestCase
{
    use RefreshDatabase, SearchableTest;

    /** @test */
    public function search_threads_title_given_a_search_term()
    {
        $undesiredThread = create(Thread::class);
        $anotherUndesiredThread = ThreadFactory::withBody($this->sentence())->create();
        $user = create(User::class);
        $desiredThread = ThreadFactory::by($user)
            ->withTitle($this->sentence())
            ->create();
        $numberOfDesiredThreads = 1;

        $results = $this->searchJson([
            'only_title' => true,
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
    public function search_thread_titles_given_multiple_usernames_and_a_search_term()
    {
        $undesiredThread = ThreadFactory::withTitle($this->sentence())->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withTitle($this->sentence())
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withTitle($this->sentence())
            ->create();
        $usernames = "{$john->name},{$doe->name}";
        $numberOfDesiredItems = 2;

        $results = $this->searchJson([
            'q' => $this->searchTerm(),
            'only_title' => true,
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsThread($results, $threadByDoe);
        $this->assertContainsThread($results, $threadByJohn);

        $this->emptyIndices();
    }

    /** @test */
    public function search_threads_title_that_were_created_a_given_number_of_days_ago_given_a_search_term_()
    {
        $user = create(User::class);
        $daysAgo = 5;
        $desiredThread = ThreadFactory::by($user)
            ->withTitle($this->sentence())
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = ThreadFactory::withBody($this->sentence())->create();
        $anotherUndesiredThread = ThreadFactory::withTitle($this->sentence())->create();
        Carbon::setTestNow();
        $numberOfDesiredThreads = 1;
        $results = $this->searchJson([
            'q' => $this->searchTerm(),
            'only_title' => true,
            'last_created' => $daysAgo,
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
    public function a_user_can_search_threads_title_given_a_search_term_and_username_that_where_created_the_last_given_number_of_days()
    {
        $user = create(User::class);
        $daysAgo = 5;
        $desiredThread = ThreadFactory::by($user)
            ->withTitle($this->sentence())
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredThread = ThreadFactory::withBody($this->sentence())->create();
        $anotherUndesiredThread = ThreadFactory::withTitle($this->sentence())->create();
        $thirdUndesiredThread = ThreadFactory::by($user)
            ->withTitle($this->sentence())
            ->create();
        Carbon::setTestNow();
        $numberOfDesiredThreads = 1;

        $results = $this->searchJson([
            'q' => $this->searchTerm(),
            'only_title' => true,
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $numberOfDesiredThreads
        );

        $this->assertCount(
            $numberOfDesiredThreads, $results
        );
        $this->assertContainsThread($results, $desiredThread);

        $this->emptyIndices();
    }

}