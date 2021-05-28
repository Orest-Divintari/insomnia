<?php

namespace Tests\Feature\Events;

use App\Listeners\Follow\DeleteFollowNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AUserUnfollowedYouEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_unfollows_another_user_then_an_event_is_fired()
    {
        $followerUser = $this->signIn();
        $listener = Mockery::spy(DeleteFollowNotification::class);
        app()->instance(DeleteFollowNotification::class, $listener);
        $followingUser = create(User::class);
        $followerUser->follow($followingUser);
        $this->assertCount(1, $followingUser->notifications);

        $this->delete(route('ajax.follow.destroy', $followingUser));

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($followingUser, $followerUser) {
                return $event->follower->id == $followerUser->id
                && $event->following->id == $followingUser->id;
            }));
    }
}