<?php

namespace Tests\Unit;

use App\Follow;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $anotherUser;
    public function setUp(): void
    {
        parent::setUp();

        $this->user = create(User::class);
        $this->anotherUser = create(User::class);
    }

    /** @test */
    public function a_user_knows_if_is_following_another_user()
    {
        $this->assertFalse(
            $this->user->following($this->anotherUser)
        );
        $this->signIn($this->user);

        $this->user->follow($this->anotherUser);

        $this->assertTrue(
            $this->user->following($this->anotherUser)
        );
    }
    /** @test */
    public function a_user_may_follow_another_user()
    {
        $this->assertCount(0, $this->user->follows);
        $this->signIn($this->user);

        $this->user->follow($this->anotherUser);

        $this->assertCount(1, $this->user->fresh()->follows);
        $this->assertTrue(
            $this->user->following($this->anotherUser)
        );
    }

    /** @test */
    public function a_user_cannot_follow_a_user_that_is_already_following()
    {
        $this->assertCount(0, $this->user->follows);
        $this->signIn($this->user);

        $this->user->follow($this->anotherUser);

        $this->assertCount(1, $this->user->fresh()->follows);
        $this->assertTrue(
            $this->user->following($this->anotherUser)
        );

        $this->user->follow($this->anotherUser);
        $this->user->follow($this->anotherUser);

        $this->assertCount(1, $this->user->fresh()->follows);
    }

    /** @test */
    public function a_user_may_toggle_the_follow_to_another_user()
    {
        $this->signIn($this->user);
        $this->user->follow($this->anotherUser);
        $this->assertTrue(
            $this->user->following($this->anotherUser)
        );

        $this->user->toggleFollow($this->anotherUser);
        $this->assertFalse(
            $this->user->following($this->anotherUser)
        );

        $this->user->toggleFollow($this->anotherUser);
        $this->assertTrue(
            $this->user->following($this->anotherUser)
        );
    }

    /** @test */
    public function a_user_may_have_followers()
    {
        $this->signIn($this->anotherUser);
        $this->anotherUser->follow($this->user);

        $this->assertCount(1, $this->user->followedBy);
        $this->user->following($this->anotherUser);
    }

    /** @test */
    public function a_user_knows_the_users_that_is_following()
    {
        $this->signIn($this->user);

        $this->user->follow($this->anotherUser);

        $this->assertCount(1, $this->user->follows);
    }

    /** @test */
    public function a_profile_owner_knows_if_is_followed_by_visitor()
    {
        $profileOwner = create(User::class);
        $visitor = $this->signIn();

        $visitor->follow($profileOwner);

        $profileOwner = User::whereId($profileOwner->id)
            ->withFollowedByVisitor()
            ->first();
        $this->assertTrue($profileOwner->followed_by_visitor);
    }

    /** @test */
    public function a_user_knows_the_number_of_followers()
    {
        $user = create(User::class);
        $john = create(User::class);
        $george = create(User::class);
        $this->signIn($john);
        $john->follow($user);
        $this->signIn($george);
        $george->follow($user);

        $this->assertEquals(
            2,
            $user->loadCount('followedBy')->followed_by_count
        );
    }

    /** @test */
    public function a_user_knows_the_number_of_users_that_follows()
    {
        $user = create(User::class);
        $john = create(User::class);
        $george = create(User::class);
        $this->signIn($user);

        $user->follow($john);
        $user->follow($george);

        $this->assertEquals(
            2,
            $user->loadCount('follows')->follows_count
        );
    }
}