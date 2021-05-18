<?php

namespace Tests\Feature;

use App\Follows;
use App\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_follow_other_users()
    {
        $user = create(User::class);
        $anotherUser = create(User::class);

        $response = $this->post(
            route('ajax.follow.store', $anotherUser),
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function guest_users_cannot_unfollow_other_users()
    {
        $user = create(User::class);
        $anotherUser = create(User::class);

        $response = $this->delete(
            route('ajax.follow.destroy', $anotherUser)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_may_follow_another_user()
    {
        $user = $this->signIn();
        $this->assertCount(0, $user->follows);
        $anotherUser = create(User::class);

        $this->post(route('ajax.follow.store', $anotherUser));

        $this->assertCount(1, $user->fresh()->follows);
        $this->assertTrue($user->fresh()->following($anotherUser));
    }

    /** @test */
    public function an_authenticated_user_may_unfollow_another_user()
    {
        $user = $this->signIn();
        $anotherUser = create(User::class);
        $this->post(
            route('ajax.follow.store', $anotherUser)
        );
        $this->assertTrue($user->fresh()->following($anotherUser));

        $this->delete(route('ajax.follow.destroy', $anotherUser));

        $this->assertFalse($user->fresh()->following($anotherUser));
    }

    /** @test */
    public function an_authenticated_user_can_follow_another_user_only_once()
    {
        $user = $this->signIn();
        $this->assertCount(0, $user->follows);
        $anotherUser = create(User::class);

        $this->post(route('ajax.follow.store', $anotherUser));
        $this->assertCount(1, $user->refresh()->follows);
        $this->post(route('ajax.follow.store', $anotherUser));

        $this->assertCount(1, $user->follows);
        $this->assertTrue($user->fresh()->following($anotherUser));
    }

    /** @test */
    public function a_member_can_view_the_paginated_list_of_followers_of_profile_user()
    {
        $profileOwner = $this->signIn();
        $followerA = create(User::class);
        $followerB = create(User::class);

        $followerA->follow($profileOwner);
        $followerB->follow($profileOwner);

        $followers = $this->getJson(
            route('ajax.followed-by.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(2, $followers);
        $this->assertEquals(
            $followerA->id, $followers[0]['id']
        );
        $this->assertEquals(
            $followerB->id, $followers[1]['id']
        );
    }

    /** @test */
    public function guests_may_not_see_the_list_of_followers_of_a_profile_user()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);

        $followerA->follow($profileOwner);
        $followerB->follow($profileOwner);

        $response = $this->getJson(
            route('ajax.followed-by.index', $profileOwner)
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_member_can_view_the_paginated_list_of_users_that_profile_user_follows()
    {
        $profileOwner = $this->signIn();
        $userA = create(User::class);
        $userB = create(User::class);

        $profileOwner->follow($userA);
        $profileOwner->follow($userB);

        $follows = $this->getJson(
            route('ajax.follows.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(2, $follows);
        $this->assertEquals(
            $userA->id, $follows[0]['id']
        );
        $this->assertEquals(
            $userB->id, $follows[1]['id']
        );
    }

    /** @test */
    public function guests_may_not_view_the_paginated_list_of_users_that_profile_user_follows()
    {
        $profileOwner = create(User::class);
        $userA = create(User::class);
        $userB = create(User::class);

        $profileOwner->follow($userA);
        $profileOwner->follow($userB);

        $response = $this->getJson(
            route('ajax.follows.index', $profileOwner)
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_member_may_view_the_like_score_of_users_that_the_profile_owner_follows()
    {
        $profileOwner = create(User::class);

        $followingUser = create(User::class);
        $reply = ReplyFactory::by($followingUser)->create();
        $anotherUser = $this->signIn();
        $reply->likedBy($anotherUser);
        $visitor = $this->signIn();

        $profileOwner->follow($followingUser);

        $response = $this->getJson(
            route('ajax.follows.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['likes_count']);
    }

    /** @test */
    public function a_member_may_view_the_like_score_of_profile_owner_followers()
    {
        $profileOwner = create(User::class);

        $follower = create(User::class);
        $reply = ReplyFactory::by($follower)->create();
        $anotherUser = $this->signIn();
        $reply->likedBy($anotherUser);
        $visitor = $this->signIn();

        $follower->follow($profileOwner);

        $response = $this->getJson(
            route('ajax.followed-by.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['likes_count']);
    }

    /** @test */
    public function a_member_may_view_the_number_of_messages_of_users_that_profile_owner_follows()
    {
        $profileOwner = create(User::class);
        $followingUser = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($followingUser)->create();
        $profileOwner->follow($followingUser);
        $this->signIn();

        $response = $this->getJson(
            route('ajax.follows.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['messages_count']);
    }

    /** @test */
    public function a_member_may_view_the_number_of_messags_of_profile_owner_followers()
    {
        $profileOwner = create(User::class);
        $follower = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($follower)->create();
        $follower->follow($profileOwner);
        $this->signIn();

        $response = $this->getJson(
            route('ajax.followed-by.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['messages_count']);
    }

}