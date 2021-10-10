<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\Category;
use App\Models\User;
use App\Notifications\YouHaveBeenMentionedInAThread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\TestsQueue;

class MentionInThreadNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestsQueue;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->desiredChannels = ['database'];
        $this->poster = $this->signIn();
        $this->mentionedUser = create(User::class);
        $category = create(Category::class);
        $this->attributes = [
            'body' => "hello @{$this->mentionedUser->name}",
            'category_id' => $category->id,
            'title' => $this->faker->sentence(),
        ];

    }

    /** @test */
    public function it_pushes_the_notification_into_the_queue()
    {
        $this->unsetFakeNotifications();
        Queue::fake();
        $queue = 'notifications';

        $this->post(route('threads.store'), $this->attributes);

        $this->assertNotificationPushedOnQueue($queue, YouHaveBeenMentionedInAThread::class);
    }

    /** @test */
    public function it_sends_a_notification_when_a_user_is_mentioned_in_a_thread()
    {
        $this->post(route('threads.store'), $this->attributes);

        $thread = $this->poster->threads()->first();
        Notification::assertSentTo(
            $this->mentionedUser,
            function (YouHaveBeenMentionedInAThread $notification, $channels)
             use ($thread) {
                return $this->desiredChannels == $channels
                && $notification->thread->is($thread)
                && $notification->threadPoster->is($this->poster);
            }
        );
    }

    /** @test */
    public function it_doesnt_send_a_notification_when_a_user_is_mentioned_in_a_thread_by_ignored_users()
    {
        $this->mentionedUser->ignore($this->poster);

        $this->post(route('threads.store'), $this->attributes);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAThread::class);
    }

    /** @test */
    public function it_doesnt_send__a_database_notification_when_a_user_is_mentioned_in_a_thread_when_notifications_are_disabled()
    {
        $this->mentionedUser->preferences()->merge(['mentioned_in_thread' => false]);
        $this->desiredChannels = [];

        $this->post(route('threads.store'), $this->attributes);

        $thread = $this->poster->threads()->first();
        $thread = $this->poster->threads()->first();
        Notification::assertSentTo(
            $this->mentionedUser,
            function (YouHaveBeenMentionedInAThread $notification, $channels)
             use ($thread) {
                return $this->desiredChannels == $channels
                && $notification->thread->is($thread)
                && $notification->threadPoster->is($this->poster);
            }
        );
    }

    /** @test */
    public function it_doesnt_send_a_notification_to_unverified_users_when_are_mentioned_in_a_thread()
    {
        $this->mentionedUser->update(['email_verified_at' => null]);

        $this->post(route('threads.store'), $this->attributes);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAThread::class);
    }

    /** @test */
    public function it_doesnt_send_a_database_notification_when_thread_posters_mention_their_own_names()
    {
        $this->attributes['body'] = "hello @{$this->poster->name}";

        $this->post(route('threads.store'), $this->attributes);

        Notification::assertNotSentTo($this->poster, YouHaveBeenMentionedInAThread::class);
    }
}