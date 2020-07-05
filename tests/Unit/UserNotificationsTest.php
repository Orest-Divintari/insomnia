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
    public function when_a_thread_has_new_reply_a_notification_is_sent_to_email_and_database()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply(raw(Reply::class));

        $notification = new ThreadHasNewReply($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));

    }

    /** @test */
    public function when_a_reply_is_liked_a_notification_is_sent_to_email_and_database()
    {
        Notification::fake();

        $user = $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class));

        $notification = new ReplyHasNewLike($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));

    }

    /** @test */
    public function when_a_thread_has_new_reply_only_database_notifications_are_stored_when_emails_are_disabled()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply(raw(Reply::class));

        $user->subscription($thread->id)->disableEmails();

        $notification = new ThreadHasNewReply($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['database'], $notification->via($user));

        $this->assertNotEquals(['mail', 'database'], $notification->via($user));
    }

    /** @test */
    public function when_a_reply_has_new_like_only_database_notifications_are_stored_when_emails_are_disabled()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply(raw(Reply::class));

        $user->subscription($thread->id)->disableEmails();

        $notification = new ReplyHasNewLike($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['database'], $notification->via($user));

        $this->assertNotEquals(['mail', 'database'], $notification->via($user));
    }

}