<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\User;
use App\Notifications\ProfileHasNewPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class YourProfileHasANewPostNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function it_sends_a_database_notification_when_a_user_creates_a_post_on_your_profile()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.profile-posts.store', $profileOwner), $attributes);

        $profilePost = $profileOwner->profilePosts()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (ProfileHasNewPost $notification, $channels)
             use ($profilePost, $desiredChannels) {
                return $notification->profilePost->id == $profilePost->id &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_a_database_notification_when_an_ignored_user_creates_a_post_on_your_profile()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profileOwner->ignore($poster);
        $attributes = ['body' => $this->faker()->sentence()];

        $this->postJson(route('ajax.profile-posts.store', $profileOwner), $attributes);

        Notification::assertNotSentTo($profileOwner, ProfileHasNewPost::class);
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_users_creates_a_post_on_their_own_profile()
    {
        $profileOwner = create(User::class);
        $this->signIn($profileOwner);
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.profile-posts.store', $profileOwner), $attributes);

        Notification::assertNotSentTo($profileOwner, ProfileHasNewPost::class);
    }

    /** @test */
    public function it_sends_database_notification_when_a_user_creates_a_post_on_your_profile_and_mentions_your_name_only_when_mention_notifications_are_disabled()
    {
        $profileOwner = create(User::class);
        $profileOwner->preferences()->merge(['mentioned_in_profile_post' => []]);
        $poster = $this->signIn();
        $attributes = ['body' => "hello @{$profileOwner->name}"];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.profile-posts.store', $profileOwner), $attributes);

        $profilePost = $profileOwner->profilePosts()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (ProfileHasNewPost $notification, $channels)
             use ($profilePost, $desiredChannels) {
                return $notification->profilePost->id == $profilePost->id &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesn_send_database_notification_when_a_user_creates_a_post_on_your_profile_and_mentions_your_name_when_mention_notifications_are_enabled()
    {
        $profileOwner = create(User::class);
        $profileOwner->preferences()->merge(['mentioned_in_profile_post' => ['database']]);
        $poster = $this->signIn();
        $attributes = ['body' => "hello @{$profileOwner->name}"];
        $desiredChannels = [];

        $this->postJson(route('ajax.profile-posts.store', $profileOwner), $attributes);

        $profilePost = $profileOwner->profilePosts()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (ProfileHasNewPost $notification, $channels)
             use ($profilePost, $desiredChannels) {
                return $notification->profilePost->id == $profilePost->id &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_doesnt_send_database_notification_when_a_user_cretes_a_post_on_your_profile_when_database_notifications_are_disabled()
    {
        $profileOwner = create(User::class);
        $profileOwner->preferences()->merge(['profile_post_created' => []]);
        $poster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.profile-posts.store', $profileOwner), $attributes);

        $profilePost = $profileOwner->profilePosts()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (ProfileHasNewPost $notification, $channels)
             use ($profilePost, $desiredChannels) {
                return $notification->profilePost->id == $profilePost->id &&
                    $desiredChannels == $channels;
            });
    }

}