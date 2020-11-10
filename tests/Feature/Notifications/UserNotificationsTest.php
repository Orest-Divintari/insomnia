<?php

namespace Tests\Feature\Notifications;

use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;
    protected $user;
    protected $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->signIn();
        $this->thread = create(Thread::class);
        $this->thread->subscribe($this->user->id);
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->response = $this->get(route('api.user-notifications.index'))->json();
    }

    /** @test */
    public function a_user_can_fetch_the_unread_database_notifications()
    {
        $this->assertCount(2, $this->response);
    }

    /** @test */
    public function a_user_can_mark_a_database_notification_as_read()
    {
        $this->assertCount(2, $this->response);

        $firstNotification = $this->user->unreadNotifications->first();

        $this->delete(route('api.user-notifications.destroy', $firstNotification->id));

        $response = $this->get(route('api.user-notifications.index'))->json();

        $this->assertCount(1, $response);

    }

}