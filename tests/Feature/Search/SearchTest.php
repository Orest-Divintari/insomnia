<?php

namespace Tests\Feature\Search;

use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $threadsToDelete;
    protected $numberOfUndesiredItems;
    protected $numberOfDesiredItems;
    protected $searchTerm;
    protected $noResultsMessage;
    public function setUp(): void
    {
        parent::setUp();
        $this->numberOfUndesiredItems = 2;
        $this->numberOfDesiredItems = 2;
        $this->numberOfItemsToDelete = $this->numberOfUndesiredItems + $this->numberOfDesiredItems;
        $this->searchTerm = 'iphone';
        $this->noResultsMessage = 'No results found.';
        config(['scout.driver' => 'elastic']);
    }

    /** @test */
    public function display_no_results_message_when_no_threads_are_found_for_the_given_search_query()
    {
        $thread = create(Thread::class);

        $this->get(route('search.index', [
            'type' => 'thread',
            'q' => $this->searchTerm,
        ]))->assertSee($this->noResultsMessage);

        $thread->delete();
    }

    /** @test */
    public function display_no_results_message_when_no_threads_are_found_for_the_given_search_query_the_last_given_number_of_days()
    {
        $oldThread = create(Thread::class, [
            'body' => $this->searchTerm,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $response = $this->get(route('search.index', [
            'type' => 'thread',
            'q' => $this->searchTerm,
            'last_created' => 1,
        ]));

        $response->assertSee($this->noResultsMessage);

        $oldThread->delete();
    }

    /** @test */
    public function display_no_results_message_when_no_threads_are_found_given_a_search_query_for_the_given_username()
    {
        $thread = create(Thread::class, [
            'body' => $this->searchTerm,
        ]);
        $name = 'benz';
        create(User::class, ['name' => $name]);

        $response = $this->get(route('search.index', [
            'type' => 'thread',
            'q' => $this->searchTerm,
            'posted_by' => $name,
        ]));

        $response->assertSee($this->noResultsMessage);

        $thread->delete();
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
                route('search.index', $parameters)
            );
            $counter++;

        } while ($this->validate($results, $numberOfItems) && $counter <= 500);

        return $results->json()['data'];
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

    /**
     * Determine whether the results contain the given thread
     *
     * @param array $results
     * @param Thread $thread
     * @return boolean
     */
    public function assertContainsThread($results, $thread)
    {
        $results = collect($results);

        $this->assertTrue(
            $results->contains(function ($result) use ($thread) {
                $categoryKeyExists = array_key_exists('category', $result) ? true : false;

                return $result['id'] == $thread->id
                && $result['poster']['id'] == $thread->poster->id
                && $categoryKeyExists
                && $result['category']['id'] == $thread->category->id;
            }));
    }

    /**
     * Determine whether the results contain the given threadReply
     *
     * @param array $results
     * @param Reply $threadReply
     * @return boolean
     */
    public function assertContainsThreadReply($results, $threadReply)
    {
        $results = collect($results);
        $this->assertTrue(
            $results->contains(function ($result) use ($threadReply) {
                return $result['id'] == $threadReply->id
                && $result['poster']['id'] == $threadReply->poster->id
                && $result['repliable']['id'] == $threadReply->repliable->id
                && $result['repliable']['poster']['id'] == $threadReply->repliable->poster->id
                && $result['repliable']['category']['id'] == $threadReply->repliable->category->id;
            }));
    }

    /**
     * Determine whether the results contain the given profilePost
     *
     * @param array $results
     * @param ProfilePost $profilePost
     * @return bool
     */
    public function assertContainsProfilePost($results, $profilePost)
    {
        $results = collect($results);
        $this->assertTrue(
            $results->contains(function ($result) use ($profilePost) {
                $profileOwnerKeyExists = array_key_exists('profile_owner_id', $result) ? true : false;

                return $result['id'] == $profilePost->id
                && $profileOwnerKeyExists
                && $result['profile_owner_id'] == $profilePost->profileOwner->id
                && $result['poster']['id'] == $profilePost->poster->id;
            }));
    }

    /**
     * Determine whether the results contain the given comment
     *
     * @param array $results
     * @param Reply $comment
     * @return bool
     */
    public function assertContainsComment($results, $comment)
    {
        $results = collect($results);
        $this->assertTrue(
            $results->contains(function ($result) use ($comment) {
                $repliableKeyExists = array_key_exists('repliable', $result) ? true : false;

                return $result['id'] == $comment->id
                && $result['poster']['id'] == $comment->poster->id
                && $repliableKeyExists
                && $result['repliable']['id'] == $comment->repliable->id
                && $result['repliable']['poster']['id'] == $comment->repliable->poster->id
                && $result['repliable']['profile_owner_id'] == $comment->repliable->profileOwner->id;
            }));
    }

}