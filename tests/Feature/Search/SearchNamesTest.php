<?php

namespace Tests\Feature\Search;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SearchableTest;

class SearchNamesTest extends TestCase
{
    use RefreshDatabase, SearchableTest;

    /** @test */
    public function it_searches_usernames()
    {
        config(['scout.driver' => 'elastic']);
        create(User::class, ['name' => 'alex']);
        create(User::class, ['name' => 'alexander']);
        create(User::class, ['name' => 'alexei']);
        create(User::class, ['name' => 'bingoalex']);
        create(User::class, ['name' => 'orestis']);

        $counter = 0;
        do {
            sleep(0.2);
            $counter++;
            $results = $this->getJson(route('ajax.search.names.index', ['name' => 'alex']))->json();
        } while (count($results) !== 3 || $counter < 40);

        $this->assertCount(3, $results);
        foreach ($results as $result) {
            $this->assertTrue(str_starts_with($result, 'alex'));
        }

        $this->emptyIndices();
    }

    /** @test */
    public function it_does_not_search_names_of_unverified_users()
    {
        config(['scout.driver' => 'elastic']);
        create(User::class, ['name' => 'alex', 'email_verified_at' => null]);
        create(User::class, ['name' => 'alexander']);
        create(User::class, ['name' => 'alexei']);
        create(User::class, ['name' => 'bingoalex']);
        create(User::class, ['name' => 'orestis']);

        $counter = 0;
        do {
            usleep(200000);
            $counter++;
            $results = $this->getJson(route('ajax.search.names.index', ['name' => 'alex']))->json();
        } while (count($results) !== 2 || $counter < 10);

        $this->assertCount(2, $results);
        foreach ($results as $result) {
            $this->assertTrue(str_starts_with($result, 'alex'));
        }

        $this->emptyIndices();
    }

}