<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Models\ProfilePost;
use App\Models\User;
use App\Notifications\YouHaveBeenMentionedInAProfilePost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MentionInProfilePostNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->desiredChannels = ['database'];
        $this->postPoster = $this->signIn();
        $this->mentionedUser = create(User::class);
    }

    /** @test */
    public function users_that_are_mentioned_in_a_profile_post_that_is_not_on_their_profile_may_receive_a_database_notification()
    {
        $profilePost = ['body' => "hello @{$this->mentionedUser->name}"];

        $profilePost = $this->post(route('ajax.profile-posts.store', $this->postPoster), $profilePost);

        $profilePost = $this->postPoster->profilePosts->first();
        Notification::assertSentTo(
            $this->mentionedUser,
            function (YouHaveBeenMentionedInAProfilePost $notification, $channels)
             use ($profilePost) {
                return $notification->profilePost->is($profilePost) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function it_sends_notifications_only_to_the_newly_mentioned_users_when_a_comment_is_updated()
    {
        unset(app()[ChannelManager::class]);
        $this->withoutMiddleware([ThrottlePosts::class]);
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $poster = $this->signIn();
        $profileOwner = create(User::class);
        $mentionedUser = create(User::class);
        $post = ['body' => "hello @{$mentionedUser->name}"];
        $newMentionedUser = create(User::class);

        $post = $this->postJson(route('ajax.profile-posts.store', $profileOwner), $post)->json();

        $this->assertCount(1, $mentionedUser->notifications);
        $this->assertEquals($post['id'], $mentionedUser->notifications()->first()['data']['profilePost']['id']);
        $this->assertCount(0, $newMentionedUser->notifications);

        $updatedPost = ['body' => "hello @{$mentionedUser->name} and @{$newMentionedUser->name}"];
        $post = ProfilePost::find($post['id']);

        $this->patchJson(route('ajax.profile-posts.update', $post), $updatedPost);

        $this->assertCount(1, $mentionedUser->fresh()->notifications);
        $this->assertEquals($post->id, $mentionedUser->notifications()->first()['data']['profilePost']['id']);
        $this->assertCount(1, $newMentionedUser->fresh()->notifications);
        $this->assertEquals($post->id, $newMentionedUser->notifications()->first()['data']['profilePost']['id']);

        $post->delete();
        $mentionedUser->delete();
        $poster->delete();
        $profileOwner->delete();
        $newMentionedUser->delete();
    }

    /** @test */
    public function unverified_users_should_not_receive_a_notification_when_are_mentioned_in_a_profile_post()
    {
        $this->mentionedUser->update(['email_verified_at' => null]);
        $profilePost = ['body' => "hello @{$this->mentionedUser->name}"];

        $this->post(route('ajax.profile-posts.store', $this->postPoster), $profilePost);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAProfilePost::class);
    }

    /** @test */
    public function when_mention_notifications_are_disabled_then_users_will_not_receive_a_database_notification_when_are_mentioned_in_a_post()
    {
        $this->mentionedUser->preferences()
            ->merge(['mentioned_in_profile_post' => []]);
        $profilePost = ['body' => "hello @{$this->mentionedUser->name}"];
        $this->desiredChannels = [];

        $this->post(route('ajax.profile-posts.store', $this->postPoster), $profilePost);

        $profilePost = $this->postPoster->profilePosts->first();
        Notification::assertSentTo(
            $this->mentionedUser,
            function (YouHaveBeenMentionedInAProfilePost $notification, $channels)
             use ($profilePost) {
                return $notification->profilePost->is($profilePost) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function users_dont_receive_database_notifications_when_are_mentioned_by_ignored_users()
    {
        $this->mentionedUser->ignore($this->postPoster);
        $profilePost = ['body' => "hello @{$this->mentionedUser->name}"];
        $this->desiredChannels = [];

        $this->post(route('ajax.profile-posts.store', $this->postPoster), $profilePost);

        Notification::assertNotSentTo($this->mentionedUser, YouHaveBeenMentionedInAProfilePost::class);
    }

    /** @test */
    public function when_users_mention_their_own_name_in_a_profile_post_they_will_not_get_a_database_notification()
    {
        $postPoster = create(User::class, ['name' => 'orestis']);
        $this->signIn($postPoster);
        $profilePost = ['body' => "hello @{$postPoster->name}"];

        $this->post(route('ajax.profile-posts.store', $postPoster), $profilePost);

        Notification::assertNotSentTo($postPoster, YouHaveBeenMentionedInAProfilePost::class);
    }

    /** @test */
    public function profile_owners_may_receive_database_notifications_when_are_mentioned_in_a_post_on_their_own_profile_only_when_profile_post_created_notifications_are_disabled()
    {
        $profileOwnerAndMentionedUser = create(User::class, ['name' => 'orestis']);
        $profileOwnerAndMentionedUser->preferences()->merge(['profile_post_created' => []]);
        $profilePost = ['body' => "hello @{$profileOwnerAndMentionedUser->name}"];

        $this->post(route('ajax.profile-posts.store', $profileOwnerAndMentionedUser), $profilePost);

        $profilePost = $profileOwnerAndMentionedUser->profilePosts->first();
        Notification::assertSentTo(
            $profileOwnerAndMentionedUser,
            function (YouHaveBeenMentionedInAProfilePost $notification, $channels)
             use ($profilePost) {
                return $notification->profilePost->is($profilePost) &&
                $this->desiredChannels == $channels;
            });
    }

    /** @test */
    public function profile_owners_should_not_receive_database_notifications_when_are_mentioned_in_a_post_on_their_own_profile_when_the_profile_post_created_notifications_are_enabled()
    {
        $profileOwnerAndMentionedUser = create(User::class, ['name' => 'orestis']);
        $profileOwnerAndMentionedUser->preferences()->merge(['profile_post_created' => ['database']]);
        $profilePost = ['body' => "hello @{$profileOwnerAndMentionedUser->name}"];
        $this->desiredChannels = [];

        $this->post(route('ajax.profile-posts.store', $profileOwnerAndMentionedUser), $profilePost);

        $profilePost = $profileOwnerAndMentionedUser->profilePosts->first();
        Notification::assertSentTo(
            $profileOwnerAndMentionedUser,
            function (YouHaveBeenMentionedInAProfilePost $notification, $channels)
             use ($profilePost) {
                return $notification->profilePost->is($profilePost) &&
                $this->desiredChannels == $channels;
            });
    }
}