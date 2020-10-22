<?php

namespace Tests\Feature;

use App\User;
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
}