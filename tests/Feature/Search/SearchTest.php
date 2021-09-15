<?php

namespace Tests\Feature\Search;

use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\SearchableTest;

class SearchTest extends TestCase
{
    use RefreshDatabase, WithFaker, SearchableTest;

    /** @test */
    public function display_no_results_message_when_no_threads_are_found_for_the_given_search_query()
    {
        $thread = create(Thread::class);

        $response = $this->searchNoResults([
            'type' => 'thread',
            'q' => $this->searchTerm(),
        ],
            $this->noResultsMessage()
        );

        $response->assertSee($this->noResultsMessage());

    }

    /** @test */
    public function display_no_results_message_when_no_threads_are_found_for_the_given_search_query_the_last_given_number_of_days()
    {
        $term = $this->faker()->sentence();
        $oldThread = create(Thread::class, [
            'body' => $term,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $response = $this->searchNoResults([
            'type' => 'thread',
            'q' => $term,
            'last_created' => 1,
        ],
            $this->noResultsMessage()
        );

        $response->assertSee($this->noResultsMessage());

    }

    /** @test */
    public function display_no_results_message_when_no_threads_are_found_given_a_search_query_for_the_given_username()
    {
        $thread = create(Thread::class, [
            'body' => $this->sentence(),
        ]);
        $name = 'benz';
        create(User::class, ['name' => $name]);

        $response = $this->searchNoResults([
            'type' => 'thread',
            'q' => $this->searchTerm(),
            'posted_by' => $name,
        ],
            $this->noResultsMessage()
        );

        $response->assertSee($this->noResultsMessage());

    }

    /** @test */
    public function when_the_given_username_is_not_found_an_error_must_be_thrown()
    {
        $username = $this->faker->userName();
        $error = 'The following member could not be found: ' . $username;

        $response = $this->get(route('search.index', [
            'posted_by' => $username,
        ]));

        $response->assertSee($error);
    }

    /** @test */
    public function when_one_of_the_given_usernames_is_not_found_an_error_must_be_thrown()
    {
        $user = create(User::class);
        $fakeUserName = $this->faker->userName();
        $usernames = $user->name . ',' . $fakeUserName;
        $error = 'The following member could not be found: ' . $fakeUserName;

        $response = $this->get(route('search.index', [
            'posted_by' => $fakeUserName,
        ]));

        $response->assertSee($error);
    }

    /** @test */
    public function when_none_of_the_give_usernames_are_found_an_error_must_be_thrown_for_each()
    {
        $johnFake = $this->faker->userName();
        $doeFake = $this->faker->userName();
        $usernames = $johnFake . ',' . $doeFake;
        $errorForJohn = 'The following member could not be found: ' . $johnFake;
        $errorForDoe = 'The following member could not be found: ' . $doeFake;

        $response = $this->get(route('search.index', [
            'posted_by' => $usernames,
        ]));

        $response->assertSee($errorForJohn);
        $response->assertSee($errorForDoe);
    }

    /** @test */
    public function display_the_advanced_search_for_all_posts()
    {
        $response = $this->get(route('search.advanced'), ['type' => '']);

        $response->assertOk();
    }

    /** @test */
    public function display_the_advanced_search_for_threads()
    {
        $response = $this->get(route('search.advanced'), ['type' => 'thread']);

        $response->assertOk();
        $response->assertSee('thread');
    }

    /** @test */
    public function display_the_advanced_search_for_profile_posts()
    {
        $response = $this->get(route('search.advanced'), ['type' => 'profile_post']);

        $response->assertOk();
        $response->assertSee('profile_post');
    }

    /** @test */
    public function display_the_advanced_search_for_tags()
    {
        $response = $this->get(route('search.advanced'), ['type' => 'tag']);

        $response->assertOk();
        $response->assertSee('tag');
    }

    /** @test */
    public function when_searching_a_query_or_a_username_must_be_specified()
    {
        $this->get(route('search.index'))
            ->assertSee('Please specify a search query or the name of a member.');
    }

    /** @test */
    public function when_searching_without_entering_a_search_query_then_an_existing_username_must_be_specified()
    {
        $user = create(User::class, ['name' => 'george']);
        $nonExistingUsername = 'uric';

        $this->get(route('search.index', [
            'posted_by' => $nonExistingUsername,
        ]))->assertSee('The following member could not be found: ' . $nonExistingUsername);

        $this->emptyIndices();
    }

    /** @test */
    public function when_a_search_type_is_specified_it_must_be_one_of_the_specified_values()
    {
        $this->get(route(
            'search.index',
            ['q' => 'something', 'type' => 'asdfs']
        ))->assertSee(
            'The following search type could not be found: ' . request('type')
        );
    }

}