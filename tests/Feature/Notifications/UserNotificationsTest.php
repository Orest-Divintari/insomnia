<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\AppendVisitor;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $thread;
    protected $user;
    protected $response;

    /** @test */
    public function a_user_can_fetch_the_unread_database_notifications()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        $thread->addReply(['body' => $this->faker->sentence], $john);

        $response = $this->get(route('ajax.user-notifications.index'))->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_mark_a_database_notification_as_read()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        $thread->addReply(['body' => $this->faker->sentence], $john);
        $notification = $orestis->unreadNotifications->first();

        $this->delete(route('ajax.user-notifications.destroy', $notification->id));

        $response = $this->get(route('ajax.user-notifications.index'))->json();
        $this->assertCount(0, $response);
    }

    /** @test */
    public function a_user_can_mark_notifications_as_viewed()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        $thread->addReply(['body' => $this->faker->sentence], $john);
        Carbon::setTestNow(Carbon::now()->addDay());

        $this->get(route('ajax.user-notifications.index'))->json();

        $this->assertTrue($orestis->notificationsViewed());
    }

}