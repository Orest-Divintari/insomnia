<?php

namespace Tests\Feature\Notifications;

use App\Http\Middleware\ThrottlePosts;
use App\Notifications\ProfileHasNewPost;
use App\Notifications\ProfilePostHasNewComment;
use App\ProfilePost;
use App\Reply;
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
    public function the_owner_of_a_profile_receives_notification_when_a_new_post_is_added_to_profile()
    {
        $poster = $this->signIn();
        $profileOwner = create(User::class);
        $post = ['body' => $this->faker->sentence];

        $this->post(
            route('ajax.profile-posts.store', $profileOwner),
            $post
        );

        $profilePost = ProfilePost::whereBody($post['body'])->first();
        Notification::assertSentTo(
            $profileOwner,
            ProfileHasNewPost::class,
            function ($notificiation) use (
                $poster,
                $profileOwner,
                $profilePost
            ) {
                return $notificiation->postPoster->id == $poster->id
                && $notificiation->profileOwner->id == $profileOwner->id
                && $notificiation->profilePost->id == $profilePost->id;
            }
        );
    }

    /** @test */
    public function participants_in_a_profile_post_receive_notifications_when_a_new_comment_is_added_to_the_post()
    {
        $commentPoster = $this->signIn();
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $postParticipant = create(User::class);
        CommentFactory::by($postParticipant)
            ->toProfilePost($profilePost)
            ->create();
        $comment = ['body' => $this->faker->sentence];

        $this->post(route('ajax.comments.store', $profilePost), $comment);

        $comment = Reply::whereBody($comment['body'])->first();
        Notification::assertSentTo(
            $postParticipant,
            ProfilePostHasNewComment::class,
            function ($notificiation) use (
                $commentPoster,
                $comment,
                $profilePost,
                $profileOwner
            ) {
                return $notificiation->commentPoster->id == $commentPoster->id
                && $notificiation->comment->id == $comment->id
                && $notificiation->profilePost->id == $profilePost->id
                && $notificiation->profileOwner->id == $profileOwner->id;
            }
        );

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
            ProfilePostHasNewComment::class
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
            ProfilePostHasNewComment::class
        );
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
            ProfilePostHasNewComment::class
        );
    }
}
