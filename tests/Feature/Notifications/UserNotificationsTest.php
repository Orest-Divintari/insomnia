<?php

namespace Tests\Feature\Notifications;

use App\Models\Thread;
use App\Models\User;
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
    public function it_returns_unread_notifications_up_to_one_week_old()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        Carbon::setTestNow(Carbon::now()->subMonth());
        $thread->addReply(['body' => $this->faker->sentence], $john);
        Carbon::setTestNow();
        $thread->addReply(['body' => $this->faker->sentence], $john);

        $notification = $this->get(route('ajax.user-notifications.index'))->json()[0];

        $this->assertEquals($notification['id'], $orestis->notifications()->first()->id);
        $this->assertNull($notification['read_at']);
    }

    /** @test */
    public function it_returns_read_notifications_up_to_one_week_old()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        Carbon::setTestNow(Carbon::now()->subMonth());
        $thread->addReply(['body' => $this->faker->sentence], $john);
        Carbon::setTestNow();
        $thread->addReply(['body' => $this->faker->sentence], $john);
        $orestis->notifications()->first()->markAsRead();

        $notification = $this->get(route('ajax.user-notifications.index'))->json()[0];

        $this->assertEquals($notification['id'], $orestis->notifications()->first()->id);
        $this->assertNotNull($notification['read_at']);
    }
}
