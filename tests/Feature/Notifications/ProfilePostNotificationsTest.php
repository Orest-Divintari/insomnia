<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Notifications\ParticipatedProfilePostHasNewComment;
use App\Notifications\PostOnYourProfileHasNewComment;
use App\Notifications\ProfileHasNewPost;
use App\Notifications\YourPostOnYourProfileHasNewComment;
use App\Notifications\YourProfilePostHasNewComment;
use App\ProfilePost;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfilePostNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function profile_owners_may_receive_database_notification_when_another_user_posts_on_their_profile()
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
    public function profile_owners_may_disable_the_database_notification_for_new_profile_posts_on_their_profile()
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

    /** @test */
    public function profile_owners_may_receive_database_notification_when_a_user_adds_a_comment_on_their_post()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::by($profileOwner)
            ->toProfile($profileOwner)
            ->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (YourPostOnYourProfileHasNewComment $notification, $channels)
             use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {

                return $notification->profilePost->is($profilePost) &&
                $notification->commentPoster->is($commentPoster) &&
                $notification->comment->is($comment) &&
                $notification->profileOwner->is($profileOwner) &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function profile_owners_may_disable_database_notification_when_a_user_adds_a_comment_on_their_post()
    {
        $profileOwner = create(User::class);
        $profileOwner->preferences()->merge(['comment_on_your_post_on_your_profile_created' => []]);
        $profilePost = ProfilePostFactory::by($profileOwner)
            ->toProfile($profileOwner)
            ->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (YourPostOnYourProfileHasNewComment $notification, $channels)
             use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {
                return $notification->profilePost->is($profilePost) &&

                $notification->commentPoster->is($commentPoster) &&
                $notification->comment->is($comment) &&
                $notification->profileOwner->is($profileOwner) &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function profile_owners_may_receive_database_notifications_when_a_user_adds_a_comment_on_a_post_on_their_profile()
    {
        $profileOwner = create(User::class);
        $postPoster = create(User::class);
        $profilePost = ProfilePostFactory::by($postPoster)
            ->toProfile($profileOwner)
            ->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (PostOnYourProfileHasNewComment $notification, $channels)
             use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {
                return $notification->profilePost->is($profilePost) &&

                $notification->commentPoster->is($commentPoster) &&
                $notification->comment->is($comment) &&
                $notification->profileOwner->is($profileOwner) &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function profile_owners_may_disable_database_notifications_when_a_user_adds_a_comment_on_a_post_on_their_profile()
    {
        $profileOwner = create(User::class);
        $profileOwner->preferences()->merge(['comment_on_a_post_on_your_profile_created' => []]);
        $postPoster = create(User::class);
        $profilePost = ProfilePostFactory::by($postPoster)
            ->toProfile($profileOwner)
            ->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->first();
        Notification::assertSentTo(
            $profileOwner,
            function (PostOnYourProfileHasNewComment $notification, $channels)
             use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {

                return $notification->profilePost->is($profilePost) &&
                $notification->commentPoster->is($commentPoster) &&
                $notification->comment->is($comment) &&
                $notification->profileOwner->is($profileOwner) &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function the_user_who_added_the_comment_should_not_receive_notifications_of_their_own_comments()
    {
        $commentPoster = $this->signIn();
        $profileOwner = create(User::class);
        $profilePost = create(ProfilePost::class);
        $comment = ['body' => 'some comment'];

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        Notification::assertNotSentTo(
            $commentPoster,
            ParticipatedProfilePostHasNewComment::class
        );
    }

    /** @test */
    public function the_owner_of_the_profile_should_receive_notifications_of_new_comments_added_on_profile_posts_on_his_profile()
    {
        $this->signIn();
        $profileOwner = create(User::class, [
            'name' => 'profile owner',
        ]);
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);
        $comment = ['body' => 'some comment'];

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        Notification::assertSentTo(
            $profileOwner,
            PostOnYourProfileHasNewComment::class
        );
    }

    /** @test */
    public function the_owner_of_a_profile_post_may_receive_database_notifications_when_another_user_adds_a_comment_on_that_post()
    {
        $profileOwner = create(User::class);
        $postPoster = create(User::class);
        $profilePost = ProfilePostFactory::by($postPoster)
            ->toProfile($profileOwner)
            ->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->first();
        Notification::assertSentTo($postPoster, function (YourProfilePostHasNewComment $notification, $channels) use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {
            return $notification->profilePost->is($profilePost) &&
            $notification->commentPoster->is($commentPoster) &&
            $notification->comment->is($comment) &&
            $notification->profileOwner->is($profileOwner) &&
                $desiredChannels == $channels;
        });
    }

    /** @test */
    public function the_owner_of_a_profile_post_may_disable_database_notifications_when_another_user_adds_a_comment_on_that_post()
    {
        $profileOwner = create(User::class);
        $postPoster = create(User::class);
        $postPoster->preferences()->merge(['comment_on_your_profile_post_created' => []]);
        $profilePost = ProfilePostFactory::by($postPoster)
            ->toProfile($profileOwner)
            ->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->first();
        Notification::assertSentTo($postPoster, function (YourProfilePostHasNewComment $notification, $channels) use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {
            return $notification->profilePost->is($profilePost) &&
            $notification->commentPoster->is($commentPoster) &&
            $notification->comment->is($comment) &&
            $notification->profileOwner->is($profileOwner) &&
                $desiredChannels == $channels;
        });
    }

    /** @test */
    public function a_post_participant_may_receive_database_notifications_when_another_user_adds_a_comment_on_that_post()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $postParticipant = create(User::class);
        CommentFactory::toProfilePost($profilePost)->by($postParticipant)->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->latest('id')->first();
        Notification::assertSentTo(
            $postParticipant,
            function (ParticipatedProfilePostHasNewComment $notification, $channels)
             use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {

                return $notification->profilePost->is($profilePost) &&
                $notification->commentPoster->is($commentPoster) &&
                $notification->comment->is($comment) &&
                $notification->profileOwner->is($profileOwner) &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function a_post_participant_may_disable_database_notifications_when_another_user_adds_a_comment_on_that_post()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $postParticipant = create(User::class);
        $postParticipant->preferences()->merge(['comment_on_participated_profile_post_created' => []]);
        CommentFactory::toProfilePost($profilePost)->by($postParticipant)->create();
        $commentPoster = $this->signIn();
        $attributes = ['body' => $this->faker()->sentence()];
        $desiredChannels = [];

        $this->postJson(route('ajax.comments.store', $profilePost), $attributes)->json();

        $comment = $profilePost->comments()->latest('id')->first();
        Notification::assertSentTo(
            $postParticipant,
            function (ParticipatedProfilePostHasNewComment $notification, $channels)
             use ($profilePost, $comment, $commentPoster, $profileOwner, $desiredChannels) {
                return $notification->profilePost->is($profilePost) &&

                $notification->commentPoster->is($commentPoster) &&
                $notification->comment->is($comment) &&
                $notification->profileOwner->is($profileOwner) &&
                    $desiredChannels == $channels;
            });
    }

    /** @test */
    public function the_owner_of_the_profile_should_not_receive_notifications_of_his_own_comments_on_posts_on_his_profile()
    {
        $profileOwner = $this->signIn();
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);
        $comment = ['body' => 'some comment'];

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        Notification::assertNotSentTo(
            $profileOwner,
            PostOnYourProfileHasNewComment::class
        );
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_their_own_post_on_their_profile_a_new_comment_is_added_by_an_ingored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $profilePost = ProfilePostFactory::by($john)->toProfile($john)->create();
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, YourPostOnYourProfileHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_their_profile_on_a_profile_post_by_another_user_a_new_comment_is_added_by_an_ingored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $doe = $this->signIn();
        $profilePost = ProfilePostFactory::by($doe)->toProfile($john)->create();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, PostOnYourProfileHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_their_profile_post_on_another_user_profile_a_new_comment_is_added_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $bob = create(User::class);
        $doe = $this->signIn();
        $profilePost = ProfilePostFactory::by($john)->toProfile($bob)->create();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, YourProfilePostHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_a_profile_post_that_the_user_has_commented_a_new_comment_is_added_by_an_ingored_user_adds_a_commentwhen_their_profile_post_on_another_user_profile_a_new_comment_is_added_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $bob = create(User::class);
        $profilePost = ProfilePostFactory::by($bob)->toProfile($bob)->create();
        CommentFactory::by($john)->toProfilePost($profilePost);
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, ParticipatedProfilePostHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_their_profile_is_created_a_post_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.profile-posts.store', $john), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, ProfileHasNewPost::class);
    }

}