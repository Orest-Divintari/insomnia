<?php

namespace Tests\Feature\Search;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchNamesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_searches_usernames()
    {
        config(['scout.driver' => 'elastic']);
        create(User::class, ['name' => 'alex']);
        create(User::class, ['name' => 'alexander']);
        create(User::class, ['name' => 'alexei']);
        create(User::class, ['name' => 'bingoalex']);
        create(User::class, ['name' => 'orestis']);

        do {
            sleep(0.2);
            $results = $this->getJson(route('ajax.search.names.index', ['name' => 'alex']))->json();
        } while (count($results) !== 3);

        $this->assertCount(3, $results);
        foreach ($results as $result) {
            $this->assertTrue(str_starts_with($result, 'alex'));
        }
    }

}