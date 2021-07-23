<?php

namespace Tests\Feature\Account;

use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_shows_unread_notifications_up_to_one_week_old()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        Carbon::setTestNow(Carbon::now()->subMonth());
        $oldReply = $thread->addReply(['body' => $this->faker->sentence], $john);
        Carbon::setTestNow();
        $recentReply = $thread->addReply(['body' => $this->faker->sentence], $john);

        $response = $this->get(route('account.notifications.index'));

        $response->assertSee($recentReply->body)
            ->assertDontSee($oldReply->body);
    }

    /** @test */
    public function it_shows_read_notifications_up_to_one_week_old()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        Carbon::setTestNow(Carbon::now()->subMonth());
        $oldReply = $thread->addReply(['body' => $this->faker->sentence], $john);
        Carbon::setTestNow();
        $recentReply = $thread->addReply(['body' => $this->faker->sentence], $john);
        $orestis->notifications()->first()->markAsRead();

        $response = $this->get(route('account.notifications.index'));

        $response->assertSee($recentReply->body)
            ->assertDontSee($oldReply->body);
    }
}
