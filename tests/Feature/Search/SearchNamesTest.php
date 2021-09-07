<?php

namespace Tests\Feature\Search;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchNamesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_searches_usernames()
    {
        config(['scout.driver' => 'elastic']);

        create(User::class, ['name' => 'alex']);
        create(User::class, ['name' => 'alexander']);
        create(User::class, ['name' => 'alexei']);
        create(User::class, ['name' => 'alexmark']);
        create(User::class, ['name' => 'alexbitzo']);
        create(User::class, ['name' => 'alexidis']);
        create(User::class, ['name' => 'alexiou']);
        create(User::class, ['name' => 'alexese']);
        create(User::class, ['name' => 'alextris']);
        create(User::class, ['name' => 'alexbingo']);

        $results = $this->getJson(route('ajax.search.names.index', ['name' => 'alex']))->json();

        $this->assertCount(10, $results);
        foreach ($results as $result) {
            $this->assertTrue(str_starts_with($result, 'alex'));
        }
    }

}