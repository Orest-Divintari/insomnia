<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\User;
use App\Notifications\YourProfilePostHasNewComment;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\TestsQueue;

class YourProfilePostHasNewCommentNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestsQueue;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->profileOwner = create(User::class);
        $this->postPoster = create(User::class);
        $this->profilePost = ProfilePostFactory::by($this->postPoster)
            ->toProfile($this->profileOwner)
            ->create();

    }

    /** @test */
    public function it_pushes_the_notification_into_the_queue()
    {
        $this->unsetFakeNotifications();
        Queue::fake();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $queue = 'notifications';

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $this->assertNotificationPushedOnQueue($queue, YourProfilePostHasNewComment::class);
    }

    /** @test */
    public function it_sends_database_notification_when_a_user_adds_a_comment_on_your_post()
    {
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->postPoster,
            function (YourProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {
                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_a_database_notification_when_an_ignored_user_adds_a_comment_on_your_post()
    {
        $commentPoster = $this->signIn();
        $this->postPoster->ignore($commentPoster);
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        Notification::assertNotSentTo($this->postPoster, YourProfilePostHasNewComment::class);
    }

    /** @test */
    public function it_doesnt_send_a_notification_when_the_post_poster_is_also_the_comment_poster()
    {
        $this->signIn($this->postPoster);
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        Notification::assertNotSentTo($this->postPoster, YourProfilePostHasNewComment::class);
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_adds_a_comment_on_your_post_when_notifications_are_disabled()
    {
        $this->postPoster->preferences()
            ->merge(['comment_on_your_profile_post_created' => []]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => "hello @{$this->postPoster->name}"];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->postPoster,
            function (YourProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {
                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_sends_database_notification_when_a_user_adds_a_comment_and_mentions_your_name_on_your_post_only_when_mention_notifications_are_disabled()
    {
        $this->postPoster->preferences()->merge(['mentioned_in_comment' => []]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => "hello @{$this->postPoster->name}"];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->postPoster,
            function (YourProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {
                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_adds_a_comment_and_mentions_your_name_on_your_post_when_mention_notifications_are_enabled()
    {
        $this->postPoster->preferences()
            ->merge(['mentioned_in_comment' => ['database']]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => "hello @{$this->postPoster->name}"];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->postPoster,
            function (YourProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {
                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /**
     * Assert that the notification data are the expected
     *
     * @param YourProfilePostHasNewComment $notification
     * @param Reply $comment
     * @return boolean
     */
    private function assertCorrectNotificationData($notification, $comment)
    {
        return $notification->profilePost->is($comment->repliable)
        && $notification->commentPoster->is($comment->poster)
        && $notification->comment->is($comment)
        && $notification->profileOwner->is($comment->repliable->profileOwner);
    }

}