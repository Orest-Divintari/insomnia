<?php

namespace Tests\Feature\Notifications;

use App\Notifications\CommentHasNewLike;
use App\Notifications\ProfileHasNewPost;
use App\Notifications\ProfilePostHasNewComment;
use App\ProfilePost;
use App\Reply;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfilePostNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_owner_of_a_profile_receives_notification_when_a_new_post_is_added_to_profile()
    {
        Notification::fake();
        $this->signIn();
        $profileOwner = create(User::class);
        $post = ['body' => 'some body'];

        $this->post(route('api.profile-posts.store', $profileOwner), $post);

        Notification::assertSentTo(
            $profileOwner,
            ProfileHasNewPost::class
        );
    }

    /** @test */
    public function participants_in_a_post_receive_notifications_when_a_new_comment_is_added_to_the_post()
    {
        Notification::fake();
        $commentPoster = create(User::class, [
            'name' => 'azem',
        ]);
        $this->signIn($commentPoster);
        $profileOwner = create(User::class);
        $profilePost = create(ProfilePost::class);
        $postParticipant = create(User::class, [
            'name' => 'john',
        ]);
        $participantsComment = create(Reply::class, [
            'body' => 'first comment',
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $profilePost->id,
            'user_id' => $postParticipant->id,
        ]);
        $comment = ['body' => 'some comment'];

        $this->post(route('api.comments.store', $profilePost), $comment);

        Notification::assertSentTo(
            $postParticipant,
            ProfilePostHasNewComment::class
        );

    }

    /** @test */
    public function the_user_who_added_the_comment_should_not_receive_notifications_of_his_own_comments()
    {
        Notification::fake();
        $commentPoster = $this->signIn();
        $profileOwner = create(User::class);
        $profilePost = create(ProfilePost::class);
        $comment = ['body' => 'some comment'];

        $this->post(
            route('api.comments.store', $profilePost),
            $comment
        );

        Notification::assertNotSentTo(
            $commentPoster,
            ProfilePostHasNewComment::class
        );
    }

    /** @test */
    public function the_owner_of_the_profile_should_receive_notifications_of_new_comments_added_on_profile_posts_on_his_profile()
    {
        Notification::fake();
        $this->signIn();
        $profileOwner = create(User::class, [
            'name' => 'profile owner',
        ]);
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);
        $comment = ['body' => 'some comment'];

        $this->post(
            route('api.comments.store', $profilePost),
            $comment
        );

        Notification::assertSentTo(
            $profileOwner,
            ProfilePostHasNewComment::class
        );
    }

    /** @test */
    public function the_owner_of_the_profile_should_not_receive_notifications_of_his_own_comments_on_posts_on_his_profile()
    {
        Notification::fake();
        $profileOwner = $this->signIn();
        $profilePost = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
        ]);
        $comment = ['body' => 'some comment'];

        $this->post(
            route('api.comments.store', $profilePost),
            $comment
        );

        Notification::assertNotSentTo(
            $profileOwner,
            ProfilePostHasNewComment::class
        );
    }

    /** @test */
    public function the_owner_of_a_comment_should_receive_notification_when_the_comment_is_liked_by_another_user()
    {
        Notification::fake();
        $this->signIn();
        $commentPoster = create(User::class);
        $profilePost = create(ProfilePost::class);
        $comment = create(Reply::class, [
            'user_id' => $commentPoster->id,
            'repliable_id' => $profilePost->id,
            'repliable_type' => ProfilePost::class,
        ]);

        $this->post(route('api.likes.store', $comment));

        Notification::assertSentTo(
            $commentPoster,
            CommentHasNewLike::class
        );
    }

    /** @test */
    public function the_owner_of_the_comment_should_not_receive_notifications_when_he_likes_his_own_comments()
    {
        Notification::fake();
        $commentPoster = $this->signIn();
        $profilePost = create(ProfilePost::class);
        $comment = create(Reply::class, [
            'user_id' => $commentPoster->id,
            'repliable_id' => $profilePost->id,
            'repliable_type' => ProfilePost::class,
        ]);

        $this->post(route('api.likes.store', $comment));

        Notification::assertNotSentTo(
            $commentPoster,
            CommentHasNewLike::class
        );
    }

}