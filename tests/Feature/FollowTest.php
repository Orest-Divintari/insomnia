<?php

namespace Tests\Feature;

use App\Follows;
use App\ProfilePost;
use App\User;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_follow_other_users()
    {
        $user = create(User::class);
        $anotherUser = create(User::class);

        $this->post(
            route('api.follow.store'),
            ['username' => $anotherUser->name]
        )->assertRedirect('login');
    }

    /** @test */
    public function guest_users_cannot_unfollow_other_users()
    {
        $user = create(User::class);
        $anotherUser = create(User::class);

        $this->post(
            route('api.follow.destroy'),
            ['username' => $anotherUser->name]
        )->assertRedirect('login');
    }

    /** @test */
    public function a_user_cannnot_follow_a_user_that_does_not_exist()
    {
        $user = $this->signIn();

        $nonExistingUser = ['username' => 'random name'];

        $this->post(
            route('api.follow.store'),
            $nonExistingUser
        )->assertSessionHasErrors('username');
    }

    /** @test */
    public function a_user_cannot_unfollow_a_user_that_does_not_exist()
    {
        $user = $this->signIn();

        $nonExistingUser = ['username' => 'random name'];

        $this->post(
            route('api.follow.destroy'),
            $nonExistingUser
        )->assertSessionHasErrors('username');
    }

    /** @test */
    public function an_authenticated_user_may_follow_another_user()
    {
        $user = $this->signIn();

        $this->assertCount(0, $user->follows);

        $anotherUser = create(User::class);

        $this->post(
            route('api.follow.store'),
            ['username' => $anotherUser->name]
        );

        $this->assertCount(1, $user->fresh()->follows);
        $this->assertTrue($user->fresh()->following($anotherUser));
    }

    /** @test */
    public function an_authenticated_user_may_unfollow_another_user()
    {
        $user = $this->signIn();
        $anotherUser = create(User::class);

        $this->post(
            route('api.follow.store'),
            ['username' => $anotherUser->name]
        );
        $this->assertTrue($user->following($anotherUser));

        $this->post(
            route('api.follow.destroy'),
            ['username' => $anotherUser->name]
        );
        $this->assertFalse($user->following($anotherUser));
    }

    /** @test */
    public function an_authenticated_user_can_follow_another_user_only_once()
    {
        $user = $this->signIn();

        $this->assertCount(0, $user->follows);

        $anotherUser = create(User::class);

        $this->post(
            route('api.follow.store'),
            ['username' => $anotherUser->name]
        );

        $this->post(
            route('api.follow.store'),
            ['username' => $anotherUser->name]
        );

        $this->assertCount(1, $user->fresh()->follows);
        $this->assertTrue($user->fresh()->following($anotherUser));
    }

    /** @test */
    public function a_user_can_view_the_paginated_list_of_followers_of_profile_user()
    {
        $profileOwner = create(User::class);
        $followerA = create(User::class);
        $followerB = create(User::class);

        $followerA->follow($profileOwner);
        $followerB->follow($profileOwner);

        $followers = $this->getJson(
            route('api.followedBy.index', $profileOwner)
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
    public function a_user_can_view_the_paginated_list_of_users_that_profile_user_follows()
    {
        $this->withoutExceptionHandling();
        $profileOwner = create(User::class);
        $userA = create(User::class);
        $userB = create(User::class);

        $profileOwner->follow($userA);
        $profileOwner->follow($userB);

        $follows = $this->getJson(
            route('api.follows.index', $profileOwner)
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
    public function profile_visitor_can_view_the_like_score_of_users_that_the_profile_owner_follows()
    {
        $profileOwner = create(User::class);

        $followingUser = create(User::class);
        $reply = ReplyFactory::create(['user_id' => $followingUser->id]);
        $anotherUser = $this->signIn();
        $reply->likedBy($anotherUser);

        $profileOwner->follow($followingUser);

        $response = $this->getJson(
            route('api.follows.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['like_score']);
    }

    /** @test */
    public function profile_visitor_can_view_the_like_score_of_profile_owner_followers()
    {
        $profileOwner = create(User::class);

        $follower = create(User::class);
        $reply = ReplyFactory::create(['user_id' => $follower->id]);
        $anotherUser = $this->signIn();
        $reply->likedBy($anotherUser);

        $follower->follow($profileOwner);

        $response = $this->getJson(
            route('api.followedBy.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['like_score']);
    }

    /** @test */
    public function a_profile_visitor_can_view_the_number_of_messages_of_users_that_profile_owner_follows()
    {
        $profileOwner = create(User::class);

        $followingUser = create(User::class);
        $profilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $followingUser->id]
        );

        $profileOwner->follow($followingUser);

        $response = $this->getJson(
            route('api.follows.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['message_count']);
    }

    /** @test */
    public function a_profile_visitor_can_view_the_number_of_messags_of_profile_owner_followers()
    {
        $profileOwner = create(User::class);

        $follower = create(User::class);
        $profilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $follower->id]
        );

        $follower->follow($profileOwner);

        $response = $this->getJson(
            route('api.followedBy.index', $profileOwner)
        )->json()['data'];

        $this->assertEquals(1, $response[0]['message_count']);
    }

}