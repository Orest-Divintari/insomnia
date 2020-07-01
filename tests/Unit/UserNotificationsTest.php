<?php

namespace Tests\Unit;

use App\Notifications\ReplyHasNewLike;
use App\Notifications\ThreadHasNewReply;
use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_thread_is_updated_a_notification_is_sent_to_email_and_database()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $notification = new ThreadHasNewReply($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));

    }

    /** @test */
    public function when_a_reply_is_liked_a_notification_is_sent_to_email_and_database()
    {
        Notification::fake();

        $user = $this->signIn();

        $reply = create(Reply::class);

        $notification = new ReplyHasNewLike($reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }

}