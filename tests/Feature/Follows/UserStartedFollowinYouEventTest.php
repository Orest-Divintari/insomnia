<?php

namespace Tests\Feature\Follows;

use App\Listeners\Follow\NotifyFollowingUser;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UserStartedFollowinYouEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_follows_another_user_an_event_is_fired()
    {
        $followerUser = $this->signIn();
        $listener = Mockery::spy(NotifyFollowingUser::class);
        app()->instance(NotifyFollowingUser::class, $listener);
        $followingUser = create(User::class);

        $this->post(route('api.follow.store', $followingUser));

        $this->assertCount(1, $followerUser->fresh()->follows);
        $this->assertTrue($followerUser->fresh()->following($followingUser));
        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($followingUser, $followerUser) {
                return $event->follower->id == $followerUser->id
                && $event->following->id == $followingUser->id;
            }));
    }
}