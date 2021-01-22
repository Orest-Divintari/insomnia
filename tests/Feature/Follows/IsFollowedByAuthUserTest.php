<?php

namespace Tests\Feature\Follows;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IsFollowedByAuthUser extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function determine_whether_the_authenticated_user_follows_another_user()
    {
        $this->withoutExceptionHandling();
        $john = create(User::class);
        $doe = create(User::class);
        $john->follow($doe);
        $this->signIn($john);

        $response = $this->get(route('api.is-followed-by-auth-user.show', $doe))->json();

        $this->assertTrue($response['is_followed']);
    }
}