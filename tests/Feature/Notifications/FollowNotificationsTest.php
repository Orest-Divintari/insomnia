<?php

namespace Tests\Feature\Notifications;

use App\Notifications\YouHaveANewFollower;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FollowNotificationsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function notify_a_user_when_has_a_new_follower()
    {
        Notification::fake();
        $follower = $this->signIn();
        $user = create(User::class);

        $this->post(route('api.follow.store', $user->name));

        Notification::assertSentTo(
            $user,
            function (YouHaveANewFollower $notification, $channels) use ($follower, $user) {
                return $notification->following->id == $user->id
                && $notification->follower->id == $follower->id;
            });
    }

    /** @test */
    public function delete_the_follow_notification_when_the_user_is_unfollowed()
    {
        // need to set db connection to mysql in order for this test to work
        $follower = $this->signIn();
        $user = create(User::class);
        $follower->follow($user);
        $this->assertCount(1, $user->notifications);

        $this->delete(route('api.follow.destroy', $user->name));

        $this->assertCount(0, $user->fresh()->notifications);
    }

}