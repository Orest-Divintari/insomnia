<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\YouHaveANewFollower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FollowNotificationsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function users_may_receive_database_notification_when_another_user_follows_them()
    {
        Notification::fake();
        $user = create(User::class);
        $follower = $this->signIn();
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.follow.store', $user));

        Notification::assertSentTo($user, function (YouHaveANewFollower $notification, $channels) use ($user, $follower, $desiredChannels) {
            return $notification->follower->is($follower) &&
            $notification->following->is($user) &&
                $channels == $desiredChannels;
        });
    }

    /** @test */
    public function users_may_disable_database_notification_when_another_user_follows_them()
    {
        Notification::fake();
        $user = create(User::class);
        $user->preferences()->merge(['user_followed_you' => []]);
        $follower = $this->signIn();
        $desiredChannels = [];

        $this->postJson(route('ajax.follow.store', $user));

        Notification::assertSentTo($user, function (YouHaveANewFollower $notification, $channels) use ($user, $follower, $desiredChannels) {
            return $notification->follower->is($follower) &&
            $notification->following->is($user) &&
                $channels == $desiredChannels;
        });
    }

    /** @test */
    public function delete_the_follow_notification_when_the_user_is_unfollowed()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $follower = $this->signIn();
        $user = create(User::class);
        $follower->follow($user);
        $this->assertCount(1, $user->notifications);

        $this->delete(route('ajax.follow.destroy', $user->name));

        $this->assertCount(0, $user->fresh()->notifications);

        $follower->delete();
        $user->delete();
    }

    /** @test */
    public function users_will_not_receive_notifications_when_an_ignored_user_started_following_them()
    {
        Notification::fake();
        $john = create(User::class);
        $doe = $this->signIn();
        $john->ignore($doe);

        $this->post(route('ajax.follow.store', $john));

        Notification::assertNotSentTo($john, YouHaveANewFollower::class);
    }

}
