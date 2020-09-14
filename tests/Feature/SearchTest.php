<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\Thread;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected $threadsToDelete;
    protected $undesiredItems;
    protected $desiredItems;
    public function setUp(): void
    {
        parent::setUp();
        $this->undesiredItems = 2;
        $this->desiredItems = 2;
        $this->itemsToDelete = $this->undesiredItems + $this->desiredItems;
        config(['scout.driver' => 'algolia']);

    }

    /** @test */
    public function a_user_can_use_the_advance_search()
    {
        $this->get(route('search.advanced'))
            ->assertOk();
    }

    /** @test */
    public function a_user_can_search_thread_replies()
    {
        $undesiredItems = ReplyFactory::createMany($this->undesiredItems);

        $searchTerm = 'iphone';

        $desiredItems = ReplyFactory::createMany($this->desiredItems, ['body' => $searchTerm]);

        do {
            sleep(0.2);
            $results = $this->getJson(
                route('search.show', ['type' => 'thread', 'q' => $searchTerm])
            )->json()['data'];
        } while (count($results) != $this->desiredItems);

        $this->assertCount($this->desiredItems, $results);

        Thread::latest()->take(2)->get()->each->delete();

    }

    /** @test */
    public function a_user_can_search_threads_and_replies()
    {
        createMany(
            Thread::class,
            $this->undesiredItems
        );

        $searchTerm = 'voodoo';

        createMany(
            Thread::class,
            $this->desiredItems,
            ['body' => $searchTerm]
        );

        ReplyFactory::createMany(2, ['body' => $searchTerm]);

        $this->desiredItems += 2;
        $this->itemsToDelete += 2;

        do {
            sleep(0.2);
            $results = $this->getJson(
                route(
                    'search.show', ['type' => 'thread', 'q' => $searchTerm]
                ))->json()['data'];
        } while (count($results) != $this->desiredItems);

        $this->assertCount($this->desiredItems, $results);

        Thread::latest()->take($this->itemsToDelete)->get()->each->delete();

    }

    /** @test */
    public function a_user_can_search_only_threads_by_title()
    {
        $searchTerm = 'iphone';

        createMany(
            Thread::class,
            $this->undesiredItems,
            ['body' => $searchTerm]
        );

        createMany(
            Thread::class,
            $this->desiredItems,
            ['title' => $searchTerm]
        );

        do {
            sleep(0.2);
            $results = $this->getJson(
                route('search.show', ['type' => 'thread', 'q' => $searchTerm, 'only_title' => true]
                ))->json()['data'];
        } while (count($results) != $this->desiredItems);

        $this->assertCount($this->desiredItems, $results);

        Thread::latest()->take($this->itemsToDelete)
            ->get()
            ->each
            ->delete();
    }

    /** @test */
    public function a_user_can_search_profile_posts()
    {
        createMany(ProfilePost::class, $this->undesiredItems);

        $searchTerm = 'voodoo';

        createMany(
            ProfilePost::class,
            $this->desiredItems,
            ['body' => $searchTerm]
        );

        do {
            $results = $this->getJson(
                route('search.show', ['type' => 'profile_post', 'q' => $searchTerm]
                ))->json()['data'];
        } while (empty($results));

        $this->assertCount($this->desiredItems, $results);

        ProfilePost::latest()->take($this->itemsToDelete)
            ->get()
            ->each
            ->delete();

    }

    /** @test */
    public function user_can_search_profile_posts_and_comments()
    {
        createMany(
            ProfilePost::class,
            $this->undesiredItems
        );

        $searchTerm = 'voodoo';

        createMany(
            ProfilePost::class,
            $this->desiredItems,
            ['body' => $searchTerm]
        );

        CommentFactory::createMany(2, ['body' => $searchTerm]);

        $this->desiredItems += 2;
        $this->itemsToDelete += 2;

        do {
            $results = $this->getJson(
                route('search.show', ['type' => 'profile_post', 'q' => $searchTerm]
                ))->json()['data'];
        } while (empty($results));

        $this->assertCount($this->desiredItems, $results);

        ProfilePost::latest()->take($this->itemsToDelete)
            ->get()
            ->each
            ->delete();
    }

    /** @test */
    public function a_user_can_search_all_posts()
    {
        $searchTerm = 'mac';

        $totalDesiredItems = 0;
        $totalThreadsToDelete = 0;
        $totalProfilePostsToDelete = 0;

        createMany(Thread::class, $this->undesiredItems);
        $totalThreadsToDelete += $this->undesiredItems;

        createMany(ProfilePost::class, $this->undesiredItems);
        $totalProfilePostsToDelete += $this->undesiredItems;

        ReplyFactory::createMany($this->undesiredItems);
        $totalThreadsToDelete++;

        CommentFactory::createMany($this->undesiredItems);
        $totalProfilePostsToDelete++;

        createMany(Thread::class, $this->desiredItems, ['body' => $searchTerm]);
        $totalDesiredItems += $this->desiredItems;
        $totalThreadsToDelete += $this->desiredItems;

        createMany(Thread::class, $this->desiredItems, ['title' => $searchTerm]);
        $totalDesiredItems += $this->desiredItems;
        $totalThreadsToDelete += $this->desiredItems;

        createMany(ProfilePost::class, $this->desiredItems, ['body' => $searchTerm]);
        $totalDesiredItems += $this->desiredItems;
        $totalProfilePostsToDelete += $this->desiredItems;

        ReplyFactory::createMany($this->desiredItems, ['body' => $searchTerm]);
        $totalDesiredItems += $this->desiredItems;
        $totalThreadsToDelete++;

        CommentFactory::createMany($this->desiredItems, ['body' => $searchTerm]);
        $totalDesiredItems += $this->desiredItems;
        $totalProfilePostsToDelete++;

        do {
            $results = $this->getJson(
                route('search.show', ['q' => $searchTerm]
                ))->json()['data'];
        } while (count($results) != $totalDesiredItems);

        $this->assertCount($totalDesiredItems, $results);

        Thread::latest()->take($totalThreadsToDelete)->get()->each->delete();

        ProfilePost::latest()->take($totalProfilePostsToDelete)->get()->each->delete();
    }

    /** @test */
    public function tsek()
    {
        // CommentFactory::create(['body' => 'yoyo']);

        ReplyFactory::create(['body' => 'yoyo', 'position' => 2]);

        do {
            sleep(0.3);
            $results = $this->getJson(route('search.show', ['q' => 'yoyo']))
                ->json()['data'];
        } while (empty($results));

    }

}