<?php

namespace Tests\Feature\Notifications;

use App\Notifications\ReplyHasNewLike;
use App\Notifications\ThreadHasNewReply;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadNotificationsTest extends TestCase
{

    use RefreshDatabase;

    protected $thread;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->thread = create(Thread::class);

        $this->user = $this->signIn();
    }

    /** @test */
    public function a_user_subscribed_to_a_thread_receives_a_notification_when_a_new_reply_is_posted_by_another_user()
    {
        $this->put(
            route('api.thread-subscriptions.update', $this->thread),
            ['email_notifications' => true]
        );

        $this->thread->addReply(raw(Reply::class));

        Notification::assertSentTo(
            $this->user,
            ThreadHasNewReply::class,
            function ($notification, $channels) {
                return array_intersect(['mail', 'database'], $channels);
            });
    }

    /** @test */
    public function a_user_subscribed_to_a_thread_can_disable_email_notifications_when_a_new_reply_is_posted_to_the_thread()
    {

        $this->put(
            route('api.thread-subscriptions.update', $this->thread),
            ['email_notifications' => false]
        );

        $this->thread->addReply(raw(Reply::class));

        Notification::assertSentTo(
            $this->user,
            ThreadHasNewReply::class,
            function ($notification, $channels) {
                return in_array('database', $channels) && !in_array('mail', $channels);
            });
    }

    /** @test */
    public function a_user_subscribed_to_a_thread_must_not_receive_notifications_about_his_own_replies()
    {

        $this->put(route('api.thread-subscriptions.update', $this->thread));

        $this->thread->addReply(raw(Reply::class, [
            'user_id' => $this->user->id,
        ]));

        Notification::assertNotSentTo($this->user, ThreadHasNewReply::class);
    }

    /** @test */
    public function the_poster_of_a_reply_receives_notification_when_his_post_is_liked_by_another_user()
    {

        $replyPoster = create(User::class);

        $reply = $this->thread->addReply(
            raw(Reply::class, ['user_id' => $replyPoster->id]
            ));

        $this->post(route('api.likes.store', $reply));

        Notification::assertSentTo($replyPoster, ReplyHasNewLike::class);

    }

    /** @test */
    public function the_poster_of_the_reply_must_not_receive_a_notification_when_he_likes_his_own_reply()
    {

        $reply = $this->thread->addReply(
            raw(Reply::class, ['user_id' => $this->user->id]
            ));

        $this->post(route('api.likes.store', $reply));

        Notification::assertNotSentTo($this->user, ReplyHasNewLike::class);
    }

}