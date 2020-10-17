<?php

namespace Tests\Feature;

use App\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected $threadsToDelete;
    protected $numberOfUndesiredItems;
    protected $numberOfDesiredItems;
    protected $searchTerm;

    public function setUp(): void
    {
        parent::setUp();
        $this->numberOfUndesiredItems = 2;
        $this->numberOfDesiredItems = 2;
        $this->numberOfItemsToDelete = $this->numberOfUndesiredItems + $this->numberOfDesiredItems;
        $this->searchTerm = 'iphone';
        config(['scout.driver' => 'algolia']);
    }

    /** @test */
    public function when_there_are_no_threads_stored_in_the_database()
    {
        $results = $this->getJson(route('search.show', ['type' => 'thread']));

        $this->assertEquals('No results', $results->getContent());
    }

    /** @test */
    public function when_no_threads_are_found_for_the_given_username()
    {
        $thread = create(Thread::class);
        $reply = ReplyFactory::create();

        $results = $this->getJson(route('search.show', [
            'type' => 'thread',
            'postedBy' => 'benz',
        ]));

        $this->assertEquals('No results', $results->getContent());
    }

    /** @test */
    public function when_no_threads_are_created_the_last_given_number_of_days()
    {
        $oldThread = create(Thread::class, [
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $results = $this->getJson(route('search.show', [
            'type' => 'thread',
            'lastCreated' => 1,
        ]));

        $this->assertEquals('No results', $results->getContent());

    }

    /** @test */
    public function when_no_threads_are_found_for_the_given_search_query()
    {
        $thread = create(Thread::class);

        $results = $this->getJson(route('search.show', [
            'type' => 'thread',
            'q' => $this->searchTerm,
        ]));

        $this->assertEquals('No results', $results->getContent());
    }

    /** @test */
    public function when_no_threads_are_found_for_the_given_search_query_the_last_given_number_of_days()
    {
        $oldThread = create(Thread::class, [
            'body' => $this->searchTerm,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $results = $this->getJson(route('search.show', [
            'type' => 'thread',
            'q' => $this->searchTerm,
            'lastCreated' => 1,
        ]));

        $this->assertEquals('No results', $results->getContent());

    }

    /** @test */
    public function when_no_threads_are_found_given_a_search_query_for_the_given_username()
    {
        $thread = create(Thread::class, [
            'body' => $this->searchTerm,
        ]);

        $results = $this->getJson(route('search.show', [
            'type' => 'thread',
            'q' => $this->searchTerm,
            'postedBy' => 'benz',
        ]));

        $this->assertEquals('No results', $results->getContent());
    }

    // /** @test */
    // public function a_user_can_use_the_advance_search()
    // {
    //     $this->get(route('search.advanced'))
    //         ->assertOk();
    // }

    // /** @test */
    // public function a_user_can_search_thread_replies()
    // {
    //     $undesiredItems = ReplyFactory::createMany($this->numberOfUndesiredItems);
    //     $desiredItems = ReplyFactory::createMany(
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );

    //     $results = $this->search([
    //         'q' => $this->searchTerm,
    //         'type' => 'thread',
    //     ]);

    //     $this->assertCount(
    //         $this->numberOfDesiredItems,
    //         $results
    //     );

    //     Thread::latest()
    //         ->take(2)
    //         ->get()
    //         ->each
    //         ->delete();
    // }

    // /** @test */
    // public function a_user_can_search_threads_and_replies()
    // {
    //     createMany(
    //         Thread::class,
    //         $this->numberOfUndesiredItems
    //     );
    //     createMany(
    //         Thread::class,
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     ReplyFactory::createMany(
    //         2,
    //         ['body' => $this->searchTerm]
    //     );

    //     $this->numberOfDesiredItems += 2;
    //     $this->numberOfItemsToDelete += 2;

    //     $results = $this->search([
    //         'type' => 'thread',
    //         'q' => $this->searchTerm,
    //     ]);

    //     $this->assertCount(
    //         $this->numberOfDesiredItems,
    //         $results
    //     );

    //     Thread::latest()
    //         ->take($this->numberOfItemsToDelete)
    //         ->get()
    //         ->each
    //         ->delete();
    // }

    // /** @test */
    // public function a_user_can_search_only_threads_by_title()
    // {
    //     createMany(
    //         Thread::class,
    //         $this->numberOfUndesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     createMany(
    //         Thread::class,
    //         $this->numberOfDesiredItems,
    //         ['title' => $this->searchTerm]
    //     );

    //     $results = $this->search([
    //         'q' => $this->searchTerm,
    //         'type' => 'thread',
    //         'only_title' => true,
    //     ]);

    //     $this->assertCount(
    //         $this->numberOfDesiredItems,
    //         $results
    //     );

    //     Thread::latest()
    //         ->take($this->numberOfItemsToDelete)
    //         ->get()
    //         ->each
    //         ->delete();
    // }

    // /** @test */
    // public function a_user_can_search_profile_posts()
    // {
    //     createMany(
    //         ProfilePost::class,
    //         $this->numberOfUndesiredItems
    //     );
    //     createMany(
    //         ProfilePost::class,
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );

    //     $results = $this->search([
    //         'type' => 'profile_post',
    //         'q' => $this->searchTerm,
    //     ]);

    //     $this->assertCount(
    //         $this->numberOfDesiredItems,
    //         $results
    //     );

    //     ProfilePost::latest()
    //         ->take($this->numberOfItemsToDelete)
    //         ->get()
    //         ->each
    //         ->delete();
    // }

    // /** @test */
    // public function user_can_search_profile_posts_and_comments()
    // {
    //     createMany(
    //         ProfilePost::class,
    //         $this->numberOfUndesiredItems
    //     );
    //     createMany(
    //         ProfilePost::class,
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     CommentFactory::createMany(
    //         2,
    //         ['body' => $this->searchTerm]
    //     );

    //     $this->numberOfDesiredItems += 2;
    //     $this->numberOfItemsToDelete += 2;

    //     $results = $this->search([
    //         'type' => 'profile_post',
    //         'q' => $this->searchTerm,
    //     ]);

    //     $this->assertCount(
    //         $this->numberOfDesiredItems,
    //         $results
    //     );

    //     ProfilePost::latest()
    //         ->take($this->numberOfItemsToDelete)
    //         ->get()
    //         ->each
    //         ->delete();
    // }

    // /** @test */
    // public function a_user_can_search_all_posts()
    // {
    //     $totalDesiredItems = 0;
    //     $totalThreadsToDelete = 0;
    //     $totalProfilePostsToDelete = 0;

    //     createMany(Thread::class, $this->numberOfUndesiredItems);
    //     $totalThreadsToDelete += $this->numberOfUndesiredItems;

    //     createMany(ProfilePost::class, $this->numberOfUndesiredItems);
    //     $totalProfilePostsToDelete += $this->numberOfUndesiredItems;

    //     ReplyFactory::createMany($this->numberOfUndesiredItems);
    //     $totalThreadsToDelete++;

    //     CommentFactory::createMany($this->numberOfUndesiredItems);
    //     $totalProfilePostsToDelete++;

    //     createMany(
    //         Thread::class,
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     $totalDesiredItems += $this->numberOfDesiredItems;
    //     $totalThreadsToDelete += $this->numberOfDesiredItems;

    //     createMany(
    //         Thread::class,
    //         $this->numberOfDesiredItems,
    //         ['title' => $this->searchTerm]
    //     );
    //     $totalDesiredItems += $this->numberOfDesiredItems;
    //     $totalThreadsToDelete += $this->numberOfDesiredItems;

    //     createMany(
    //         ProfilePost::class,
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     $totalDesiredItems += $this->numberOfDesiredItems;
    //     $totalProfilePostsToDelete += $this->numberOfDesiredItems;

    //     ReplyFactory::createMany(
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     $totalDesiredItems += $this->numberOfDesiredItems;
    //     $totalThreadsToDelete++;

    //     CommentFactory::createMany(
    //         $this->numberOfDesiredItems,
    //         ['body' => $this->searchTerm]
    //     );
    //     $totalDesiredItems += $this->numberOfDesiredItems;
    //     $totalProfilePostsToDelete++;

    //     $this->numberOfDesiredItems = $totalDesiredItems;
    //     $results = $this->search([
    //         'q' => $this->searchTerm,
    //     ]);

    //     $this->assertCount(
    //         $this->numberOfDesiredItems,
    //         $results
    //     );

    //     Thread::latest()
    //         ->take($totalThreadsToDelete)
    //         ->get()
    //         ->each
    //         ->delete();

    //     ProfilePost::latest()
    //         ->take($totalProfilePostsToDelete)
    //         ->get()
    //         ->each
    //         ->delete();
    // }

    /**
     * Validate the results of the request
     *
     * @param mixed $results
     * @return boolean
     */
    public function validate($results, $numberOfItems)
    {
        if (!is_object(json_decode($results->getContent()))) {
            return true;
        } elseif (count($results->json()['data']) != $numberOfItems) {
            return true;
        }
    }

    /**
     * Make a search request with the given parameters
     *
     * @param array $parameters
     * @return array
     */
    public function search($parameters, $numberOfItems = null)
    {
        $numberOfItems = $numberOfItems ?: $this->numberOfDesiredItems;
        $counter = 0;
        do {
            sleep(0.2);
            $results = $this->getJson(
                route('search.show', $parameters)
            );
            $counter++;

        } while ($this->validate($results, $numberOfItems) && $counter <= 40);

        return $results->json()['data'];
    }
}