<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ElasticTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'elastic']);
    }

    public function search()
    {
        $request = app(Request::class);
        $this->signIn();

        dd($request->user());
        create(User::class, ['name' => 'alex', 'email_verified_at' => null]);
        create(User::class, ['name' => 'alexander']);
        create(User::class, ['name' => 'alexei']);
        create(User::class, ['name' => 'bingoalex']);
        create(User::class, ['name' => 'orestis']);

        $users = User::boolSearch()
            ->must('match_phrase_prefix', ['name' => 'alex'])
            ->must('exists', ['field' => 'email_verified_at'])
            ->size(10)
            ->execute()
            ->models()
            ->toArray();

        dd($users);
    }
}