<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected $threadsToDelete;
    protected $undiseredItems;
    protected $desiredItems;
    public function setUp(): void
    {
        parent::setUp();
        $this->undiseredItems = 2;
        $this->desiredItems = 2;
        $this->itemsToDelete = $this->undiseredItems + $this->desiredItems;
        config(['scout.driver' => 'algolia']);

    }

    /** @test */
    public function a_user_can_search_threads()
    {
        $user = create(User::class, ['name' => 'orestis']);

        createMany(Thread::class, $this->undiseredItems, ['user_id' => $user->id]);

        $searchTerm = 'voodoo';

        createMany(
            Thread::class,
            $this->desiredItems,
            ['body' => $searchTerm]
        );

        do {
            $results = $this->getJson(
                route('search.show', ['type' => 'thread', 'q' => $searchTerm]
                ))->json()['data'];
        } while (empty($results));

        $this->assertCount($this->desiredItems, $results);

        Thread::latest()->take($this->itemsToDelete)->unsearchable();

    }

    /** @test */
    public function a_user_can_search_profile_posts()
    {
        createMany(ProfilePost::class, $this->undiseredItems);

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

        ProfilePost::latest()->take($this->itemsToDelete)->unsearchable();

    }

    /** @test */
    public function a_user_can_search_everything()
    {
        $this->getJson(
            route('search.show', ['q' => 'asa'])
        );
    }

}