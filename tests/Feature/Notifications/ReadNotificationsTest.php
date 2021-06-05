<?php

namespace Tests\Feature\Notifications;

use App\User;
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
        $liker = create(User::class);
        $profilePost->likedBy($liker);
        $this->assertCount(1, $user->unreadNotifications);
        $unreadNotification = $user->unreadNotifications()->first();
        $this->signIn($user);

        $this->patch(route('ajax.read-notifications.update', $unreadNotification->id));

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }

    /** @test */
    public function users_may_mark_a_notification_as_unread()
    {
        $this->withoutExceptionHandling();
        $user = create(User::class);
        $profilePost = ProfilePostFactory::by($user)->create();
        $liker = create(User::class);
        $profilePost->likedBy($liker);
        $unreadNotification = $user->unreadNotifications()->first();
        $unreadNotification->markAsRead();
        $this->assertCount(0, $user->fresh()->unreadNotifications);
        $this->signIn($user);

        $this->delete(route('ajax.read-notifications.destroy', $unreadNotification->id));

        $this->assertCount(1, $user->fresh()->unreadNotifications);
    }
}