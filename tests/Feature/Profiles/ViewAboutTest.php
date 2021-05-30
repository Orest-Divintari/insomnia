<?php

namespace Tests\Feature\Profiles;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewAboutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function members_may_view_the_about_information_of_a_user()
    {
        $profileOwner = create(User::class);
        $visitor = $this->signIn();

        $response = $this->getJson(route('ajax.about.show', $profileOwner))->json();

        $this->assertTrue(array_key_exists('follows', $response));
        $this->assertTrue(array_key_exists('followedBy', $response));
        $this->assertTrue(array_key_exists('date_of_birth', $response['user']));
        $this->assertTrue(array_key_exists('permissions', $response['user']));
    }

    /** @test */
    public function guests_may_not_view_the_about_information_of_a_user()
    {
        $response = $this->getJson(route('ajax.about.show', create(User::class)));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function members_may_view_the_list_of_followers_of_profile_user()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);
        $followerA->follow($profileOwner);
        $followerB->follow($profileOwner);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('ajax.about.show', $profileOwner)
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
            '/ajax/users/' . $profileOwner->name . '/followed-by',
            $response['path']
        );
    }

    /** @test */
    public function members_may_view_the_list_of_users_that_profile_user_is_following()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);
        $profileOwner->follow($followerA);
        $profileOwner->follow($followerB);
        $visitor = $this->signIn();

        $response = $this->getJson(
            route('ajax.about.show', $profileOwner)
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
            '/ajax/users/' . $profileOwner->name . '/follows',
            $response['path']
        );
    }
}