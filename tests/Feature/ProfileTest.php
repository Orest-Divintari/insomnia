<?php

namespace Tests\Feature;

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
        $response = $this->getJson(route('profiles.show', $profileOwner))->json();

        $this->assertContains('messages_count', $response);
        $this->assertContains('likes_score', $response);
        $this->assertContains('join_date', $response);
        $this->assertContains('followed_by_visitor', $response);
    }
}