<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\User;
use App\Notifications\YourPostOnYourProfileHasNewComment;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class YourPostOnYourProfileHasNewCommentNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->profileOwner = create(User::class);
        $this->profilePost = ProfilePostFactory::by($this->profileOwner)
            ->toProfile($this->profileOwner)
            ->create();
    }

    /** @test */
    public function it_sends_a_database_notification_when_a_user_adds_a_comment_on_your_post_on_your_profile()
    {
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->profileOwner,
            function (YourPostOnYourProfileHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_an_ignored_user_adds_a_comment_on_your_post_which_is_on_your_profile()
    {
        $commentPoster = $this->signIn();
        $this->profileOwner->ignore($commentPoster);
        $attributes = ['body' => $this->faker()->sentence()];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        Notification::assertNotSentTo($this->profileOwner, YourPostOnYourProfileHasNewComment::class);
    }

    /** @test */
    public function it_doesnt_send_a_notification_when_the_profile_owner_is_also_the_comment_poster()
    {
        $this->signIn($this->profileOwner);
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        Notification::assertNotSentTo($this->profileOwner, YourPostOnYourProfileHasNewComment::class);
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_adds_a_comment_on_your_post_on_your_profile_when_database_notifications_are_disabled()
    {
        $this->profileOwner->preferences()
            ->merge(['comment_on_your_post_on_your_profile_created' => []]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->profileOwner,
            function (YourPostOnYourProfileHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_sends_database_notification_when_a_user_adds_a_comment_and_mentions_your_name_on_your_post_on_your_profile_only_when_mention_notifications_are_disabled()
    {
        $this->profileOwner->preferences()->merge(['mentioned_in_comment' => []]);
        $commentPoster = $this->signIn();
        $attributes = ['body' => "hello @{$this->profileOwner->name}"];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->profileOwner,
            function (YourPostOnYourProfileHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_adds_a_comment_and_mentions_your_name_on_your_post_on_your_profile_when_mention_notifications_are_enabled()
    {
        $this->profileOwner->preferences()
            ->merge(['mentioned_in_comment' => ['database']]);
        $attributes = ['body' => "hello @{$this->profileOwner->name}"];
        $commentPoster = $this->signIn();
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $this->profilePost), $attributes)->json();

        $comment = $this->profilePost->comments()->first();
        Notification::assertSentTo(
            $this->profileOwner,
            function (YourPostOnYourProfileHasNewComment $notification, $channels)
             use ($comment, $desiredChannels) {

                return $this->assertCorrectNotificationData($notification, $comment)
                    && $desiredChannels == $channels;
            });
    }

    /**
     * Asser that the notification data are the expected
     *
     * @param YourPostOnYourProfileHasNewComment $notification
     * @param Reply $comment
     * @return boolean
     */
    private function assertCorrectNotificationData($notification, $comment)
    {
        return $notification->profilePost->is($comment->repliable) &&
        $notification->commentPoster->is($comment->poster) &&
        $notification->comment->is($comment) &&
        $notification->profileOwner->is($comment->repliable->profileOwner);
    }
}