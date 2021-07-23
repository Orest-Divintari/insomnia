<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_may_mark_a_notification_as_read()
    {
        $user = create(User::class);
        $profilePost = ProfilePostFactory::by($user)->create();
        $liker = $this->signIn();
        $profilePost->like($liker);
        $this->assertCount(1, $user->unreadNotifications);
        $unreadNotification = $user->unreadNotifications()->first();
        $this->signIn($user);

        $this->patch(route('ajax.read-notifications.update', $unreadNotification->id));

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }

    /** @test */
    public function users_may_mark_a_notification_as_unread()
    {
        $user = create(User::class);
        $profilePost = ProfilePostFactory::by($user)->create();
        $liker = $this->signIn();
        $profilePost->like($liker);
        $unreadNotification = $user->unreadNotifications()->first();
        $unreadNotification->markAsRead();
        $this->assertCount(0, $user->fresh()->unreadNotifications);
        $this->signIn($user);

        $this->delete(route('ajax.read-notifications.destroy', $unreadNotification->id));

        $this->assertCount(1, $user->fresh()->unreadNotifications);
    }

    /** @test */
    public function users_may_mark_all_unread_notifications_as_read()
    {
        $user = create(User::class);
        $profilePost = ProfilePostFactory::by($user)->create();
        $john = $this->signIn();
        $profilePost->like($john);
        $doe = $this->signIn();
        $profilePost->like($doe);
        $this->assertCount(2, $user->fresh()->unreadNotifications);
        $this->signIn($user);

        $this->deleteJson(route('ajax.read-all-notifications.destroy'));

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
