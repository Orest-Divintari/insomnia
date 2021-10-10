<?php

namespace Tests\Feature\Notifications;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\ThreadHasNewReply;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\TestsQueue;

class ThreadNotificationsTest extends TestCase
{

    use RefreshDatabase, WithFaker, TestsQueue;

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
    public function it_pushes_the_notification_into_the_queue_when_a_new_reply_is_posted_to_thread()
    {
        $this->unsetFakeNotifications();
        Queue::fake();
        $thread = create(Thread::class);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $queue = 'notifications';

        $this->post(
            route('ajax.replies.store', $thread),
            ['body' => $newReply]
        );

        $this->assertNotificationPushedOnQueue($queue, ThreadHasNewReply::class);
    }

    /** @test */
    public function a_thread_subscriber_receives_a_mail_and_database_notifications_when_new_reply_is_posted_to_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $thread->subscribe($subscriber->id);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $desiredChannels = ['database', 'mail'];

        $this->post(
            route('ajax.replies.store', $thread),
            ['body' => $newReply]
        );

        $reply = $thread->replies->firstWhere('body', $newReply);
        Notification::assertSentTo(
            $user,
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
                && $notification->reply->is($reply)
                && $notification->thread->is($thread);
            });
    }

    /** @test */
    public function a_thread_subscriber_should_not_receive_database_notifications_if_prefers_to_get_notifications_when_mentioned_in_a_thread_reply()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class, ['name' => 'orestis']);
        $subscriber->preferences()->merge(['mentioned_in_thread_reply' => ['database']]);
        $thread->subscribe($subscriber->id);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $desiredChannels = ['mail'];

        $this->post(
            route('ajax.replies.store', $thread),
            ['body' => "hello @{$subscriber->name}"]
        );

        $reply = $thread->replies()->latest('id')->first();

        Notification::assertSentTo(
            $subscriber,
            ThreadHasNewReply::class,
            function ($notification, $channels)
             use (
                $reply,
                $thread,
                $desiredChannels
            ) {
                return
                $channels == $desiredChannels
                && $notification->reply->is($reply)
                && $notification->thread->is($thread);
            });
    }

    /** @test */
    public function a_thread_subscriber_may_disable_the_database_notifications_for_new_replies_in_the_thread()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $subscriber->preferences()->merge(['thread_reply_created' => []]);
        $thread->subscribe($subscriber->id);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $desiredChannels = ['mail'];

        $this->post(
            route('ajax.replies.store', $thread),
            ['body' => $newReply]
        );

        $reply = $thread->replies->firstWhere('body', $newReply);
        Notification::assertSentTo(
            $subscriber,
            ThreadHasNewReply::class,
            function ($notification, $channels)
             use (
                $reply,
                $thread,
                $desiredChannels
            ) {
                return
                collect($channels)->every(function ($channel) use ($desiredChannels) {
                    return collect($desiredChannels)->contains($channel);
                }) &&
                $notification->reply->is($reply) &&
                $notification->thread->is($thread);
            });
    }

    /** @test */
    public function a_thread_subscriber_can_choose_to_receive_only_database_notifications_when_a_new_reply_is_posted_to_thread()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $thread->subscribeWithoutEmailNotifications($subscriber->id);
        $replyPoster = $this->signIn();
        $newReply = 'new reply';
        $desiredChannels = ['database'];
        $undesiredChannels = ['mail'];

        $this->post(
            route('ajax.replies.store', $thread),
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
                return in_array($desiredChannels[0], $channels) &&
                !in_array($undesiredChannels[0], $channels) &&
                $notification->thread->is($thread) &&
                $notification->reply->is($reply);
            });
    }

    /** @test */
    public function users_should_not_receive_any_notifications_about_their_own_thread_replies()
    {
        $thread = create(Thread::class);
        $subscriber = create(User::class);
        $thread->subscribe($subscriber->id);
        $this->signIn($subscriber);
        $newReply = 'new reply';

        $this->post(
            route('ajax.replies.store', $thread),
            ['body' => $newReply]
        );

        Notification::assertNotSentTo($subscriber, ThreadHasNewReply::class);
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_an_ignored_user_replies_to_a_thread()
    {
        Notification::fake();
        $john = $this->signIn();
        $thread = ThreadFactory::by($john)->create();
        $doe = create(User::class);
        $this->signIn($doe);
        $john->ignore($doe);

        $this->post(route('ajax.replies.store', $thread), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, ThreadHasNewReply::class);
    }
}