<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\User;
use App\Notifications\ParticipatedProfilePostHasNewComment;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\TestsQueue;

class ParticipatedProfilePostHasNewCommentNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestsQueue;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->profileOwner = create(User::class);
        $this->profilePost = ProfilePostFactory::toProfile($this->profileOwner)->create();
        $this->postParticipant = create(User::class);
        $this->comment = CommentFactory::toProfilePost($this->profilePost)
            ->by($this->postParticipant)
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

        $this->assertNotificationPushedOnQueue($queue, ParticipatedProfilePostHasNewComment::class);
    }

    /** @test */
    public function it_sends_database_notification_when_a_user_adds_a_comment_on_a_post_you_have_participated()
    {
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->latest('id')->first();
        Notification::assertSentTo(
            $this->postParticipant,
            function (ParticipatedProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {
                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_a__database_notification_when_an_ignored_user_adds_a_comment_on_a_post_you_have_participated()
    {
        $commentPoster = $this->signIn();
        $this->postParticipant->ignore($commentPoster);
        $attributes = ['body' => $this->faker()->sentence()];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        Notification::assertNotSentTo($this->postParticipant, ParticipatedProfilePostHasNewComment::class);
    }

    /** @test */
    public function it_doesnt_send_a_notification_to_the_comment_poster()
    {
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        Notification::assertNotSentTo($commentPoster, ParticipatedProfilePostHasNewComment::class);
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_adds_a_comment_on_a_post_you_have_participated_when_notifications_are_disabled()
    {
        $this->postParticipant->preferences()
            ->merge(['comment_on_participated_profile_post_created' => []]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->latest('id')->first();
        Notification::assertSentTo(
            $this->postParticipant,
            function (ParticipatedProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_sends_database_notification_when_a_user_adds_a_comment_and_mentions_your_name_on_a_post_on_a_post_you_have_participated_only_when_mention_notifications_are_disabled()
    {
        $this->postParticipant->preferences()->merge(['mentioned_in_comment' => []]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => "hello post participant @{$this->postParticipant->name}"];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->latest('id')->first();
        Notification::assertSentTo(
            $this->postParticipant,
            function (ParticipatedProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_adds_a_comment_and_mentions_your_name_on_a_post_on_a_post_you_have_participated_only_when_mention_notifications_are_enabled()
    {
        $this->postParticipant->preferences()
            ->merge(['mentioned_in_comment' => ['database']]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => "hello post participant @{$this->postParticipant->name}"];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->latest('id')->first();
        Notification::assertSentTo(
            $this->postParticipant,
            function (ParticipatedProfilePostHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /**
     * Assert that the notification data are the expected
     *
     * @param ParticipatedProfilePostHasNewComment $notification
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