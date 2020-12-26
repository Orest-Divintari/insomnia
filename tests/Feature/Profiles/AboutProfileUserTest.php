<?php

namespace Tests\Feature\Profiles;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutProfileUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_about_tab_of_another_profile_user()
    {
        $profileOwner = create(User::class);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('api.about.show', $profileOwner)
        )->json();

        $this->assertTrue(
            array_key_exists('follows', $response)
        );
        $this->assertTrue(
            array_key_exists('followedBy', $response)
        );
    }

    /** @test */
    public function a_user_can_view_the_list_of_followers_of_profile_user()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);
        $followerA->follow($profileOwner);
        $followerB->follow($profileOwner);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('api.about.show', $profileOwner)
        )->json()['followedBy'];

        $data = $response['data'];
        $this->assertEquals(
            $data[0]['id'],
            $followerA->id
        );
        $this->assertEquals(
            $data[1]['id'],
            $followerB->id
        );
        $this->assertEquals(
            '/api/users/' . $profileOwner->name . '/followedBy',
            $response['path']
        );
    }

    /** @test */
    public function a_user_can_view_the_list_of_users_that_profile_user_is_following()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);
        $profileOwner->follow($followerA);
        $profileOwner->follow($followerB);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('api.about.show', $profileOwner)
        )->json()['follows'];

        $data = $response['data'];
        $this->assertEquals(
            $data[0]['id'],
            $followerA->id
        );
        $this->assertEquals(
            $data[1]['id'],
            $followerB->id
        );
        $this->assertEquals(
            '/api/users/' . $profileOwner->name . '/follows',
            $response['path']
        );
    }
}