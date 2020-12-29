<?php

namespace Tests\Feature\Notifications;

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
    public function a_thread_subscriber_receives_a_mail_and_database_notifications_when_new_reply_is_posted_to_thread()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $thread->subscribe($subscriber->id);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $desiredChannels = ['mail', 'database'];

        $this->post(
            route('api.replies.store', $thread),
            ['body' => $newReply]
        );

        $reply = $thread->replies->firstWhere('body', $newReply);
        Notification::assertSentTo(
            $this->user,
            ThreadHasNewReply::class,
            function ($notification, $channels)
             use (
                $reply,
                $thread,
                $desiredChannels
            ) {
                return collect($channels)->every(function ($channel) use ($desiredChannels) {
                    return collect($desiredChannels)->contains($channel);
                })
                && $notification->reply->id == $reply->id
                && $notification->thread->id == $thread->id;
            });
    }

    /** @test */
    public function a_thread_subscriber_can_choose_to_receive_only_database_notifications_when_a_new_reply_is_posted_to_thread()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $thread->subscribe($subscriber->id, $prefersEmail = false);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $desiredChannels = ['database'];
        $undesiredChannels = ['mail'];

        $this->post(
            route('api.replies.store', $thread),
            ['body' => $newReply]
        );

        $reply = $thread->replies->firstWhere('body', $newReply);
        Notification::assertSentTo(
            $subscriber,
            ThreadHasNewReply::class,
            function ($notification, $channels)
             use (
                $thread,
                $reply,
                $desiredChannels,
                $undesiredChannels
            ) {
                return in_array($desiredChannels[0], $channels)
                && !in_array($undesiredChannels[0], $channels)
                && $notification->thread->id == $thread->id
                && $notification->reply->id == $reply->id;
            });
    }

    /** @test */
    public function a_thread_subscriber_should_not_receive_any_notifications_when_posts_replies_to_thread()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $thread->subscribe($subscriber->id);
        $this->signIn($subscriber);
        $newReply = 'new reply';

        $this->post(
            route('api.replies.store', $thread),
            ['body' => $newReply]
        );

        Notification::assertNotSentTo($subscriber, ThreadHasNewReply::class);
    }
}