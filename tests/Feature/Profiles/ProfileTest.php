<?php

namespace Tests\Feature\Profiles;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_a_profile()
    {
        $user = create(User::class);

        $this->get(route('profiles.show', $user))
            ->assertSee($user->name);
    }

    /** @test */
    public function when_visiting_a_profile_all_the_necessary_information_to_display_the_profile_are_returned()
    {
        $profileOwner = create(User::class);
        $this->signIn();

        $response = $this->get(
            route('profiles.show', $profileOwner)
        );

        $response->assertSee('messages_count');
        $response->assertSee('followed_by_visitor');
        $response->assertSee('likes_count');
        $response->assertSee('join_date');
    }

    /** @test */
    public function get_the_profile_information_of_a_user()
    {
        $profileOwner = create(User::class);
        $this->signIn();

        $response = $this->get(route('api.profiles.show', $profileOwner))->json();

        $this->assertArrayHasKey('messages_count', $response);
        $this->assertArrayHasKey('likes_count', $response);
        $this->assertArrayHasKey('join_date', $response);
        $this->assertArrayHasKey('followed_by_visitor', $response);
    }
}