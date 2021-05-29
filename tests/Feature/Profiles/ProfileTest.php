<?php

namespace Tests\Feature\Profiles;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $exception;

    public function setUp(): void
    {
        parent::setUp();
        $this->exception = 'This member limits who may view their full profile.';
    }

    /** @test */
    public function guests_may_not_visit_the_profile_of_a_user()
    {
        $profileOwner = create(User::class);

        $response = $this->get(route('profiles.show', $profileOwner));

        $response->assertRedirect('login');
    }

    /** @test */
    public function only_a_member_may_visit_the_profile_of_a_user()
    {
        $profileOwner = create(User::class);
        $this->signIn();

        $response = $this->get(route('profiles.show', $profileOwner));

        $response
            ->assertOk()
            ->assertSee($profileOwner->name);
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
        $response->assertSee('received_likes_count');
        $response->assertSee('join_date');
    }

    /** @test */
    public function members_may_get_the_profile_information_of_a_user()
    {
        $profileOwner = $this->signIn();

        $response = $this->get(route('ajax.profiles.show', $profileOwner))->json();

        $this->assertArrayHasKey('messages_count', $response);
        $this->assertArrayHasKey('received_likes_count', $response);
        $this->assertArrayHasKey('join_date', $response);
        $this->assertArrayHasKey('followed_by_visitor', $response);
    }

    /** @test */
    public function guests_may_get_the_profile_information_of_a_user()
    {
        $profileOwner = create(User::class);

        $response = $this->get(route('ajax.profiles.show', $profileOwner))->json();

        $this->assertArrayHasKey('messages_count', $response);
        $this->assertArrayHasKey('received_likes_count', $response);
        $this->assertArrayHasKey('join_date', $response);
        $this->assertArrayHasKey('followed_by_visitor', $response);
    }

    /** @test */
    public function users_can_allow_noone_to_visit_their_profile()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowNoone('show_details');
        $visitor = $this->signIn();

        $response = $this->get(route('profiles.show', $profileOwner));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertSee($this->exception);
    }

    /** @test */
    public function users_can_allow_members_to_visit_their_profile()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowMembers('show_details');
        $visitor = $this->signIn();

        $response = $this->get(route('profiles.show', $profileOwner));

        $response->assertOk();
    }

    /** @test */
    public function users_can_allow_noone_else_but_the_users_they_follow_to_visit_their_profile()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowFollowing('show_details');
        $visitor = $this->signIn();

        $response = $this->get(route('profiles.show', $profileOwner));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertSee($this->exception);
    }

    /** @test */
    public function users_can_allow_the_users_they_follow_to_visit_their_profile()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowFollowing('show_details');
        $visitor = $this->signIn();
        $profileOwner->follow($visitor);

        $response = $this->get(route('profiles.show', $profileOwner));

        $response->assertOk();
    }
}