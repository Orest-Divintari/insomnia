<?php

namespace Tests\Feature\Events;

use App\Http\Middleware\ThrottlePosts;
use App\Listeners\Profile\NotifyPostParticipantsOfNewComment;
use App\Listeners\Profile\NotifyProfileOwnerOfNewCommentOnAPost;
use App\Listeners\Profile\NotifyProfileOwnerOfNewCommentOnTheirPost;
use App\Listeners\Profile\NotifyProfilePostOwnerOfNewComment;
use App\ProfilePost;
use App\Reply;
use App\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class NewCommentWasAddedToProfilePostEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function when_a_user_posts_a_comment_to_profile_post_the_participans_are_notified()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $listener = Mockery::spy(NotifyPostParticipantsOfNewComment::class);
        app()->instance(NotifyPostParticipantsOfNewComment::class, $listener);

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        $comment = Reply::whereBody($comment['body'])->first();
        $listener->shouldHaveReceived('handle', function ($event)
             use (
                $profileOwner,
                $poster,
                $profilePost,
                $comment
            ) {
                return $event->profilePost->id == $profilePost->id
                && $event->comment->id == $comment->id
                && $event->commentPoster->id == $poster->id
                && $event->profileOwner->id == $profileOwner->id;
            });
    }

    /** @test */
    public function when_a_user_posts_a_comment_to_profile_post_the_owner_of_the_post_is_notified()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $listener = Mockery::spy(NotifyProfilePostOwnerOfNewComment::class);
        app()->instance(NotifyProfilePostOwnerOfNewComment::class, $listener);

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        $comment = Reply::whereBody($comment['body'])->first();
        $listener->shouldHaveReceived('handle', function ($event)
             use (
                $profileOwner,
                $poster,
                $profilePost,
                $comment
            ) {
                return $event->profilePost->id == $profilePost->id
                && $event->comment->id == $comment->id
                && $event->commentPoster->id == $poster->id
                && $event->profileOwner->id == $profileOwner->id;
            });
    }

    /** @test */
    public function when_a_user_posts_a_comment_to_a_profile_post_then_the_profile_owner_is_notified()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $listener = Mockery::spy(NotifyProfileOwnerOfNewCommentOnAPost::class);
        app()->instance(NotifyProfileOwnerOfNewCommentOnAPost::class, $listener);

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        $comment = Reply::whereBody($comment['body'])->first();
        $listener->shouldHaveReceived('handle', function ($event)
             use (
                $profileOwner,
                $poster,
                $profilePost,
                $comment
            ) {
                return $event->profilePost->id == $profilePost->id
                && $event->comment->id == $comment->id
                && $event->commentPoster->id == $poster->id
                && $event->profileOwner->id == $profileOwner->id;
            });
    }

    /** @test */
    public function when_a_user_posts_a_comment_to_a_post_of_the_profile_owner_then_the_profile_owner_is_notified()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $listener = Mockery::spy(NotifyProfileOwnerOfNewCommentOnTheirPost::class);
        app()->instance(NotifyProfileOwnerOfNewCommentOnTheirPost::class, $listener);

        $this->post(
            route('ajax.comments.store', $profilePost),
            $comment
        );

        $comment = Reply::whereBody($comment['body'])->first();
        $listener->shouldHaveReceived('handle', function ($event)
             use (
                $profileOwner,
                $poster,
                $profilePost,
                $comment
            ) {
                return $event->profilePost->id == $profilePost->id
                && $event->comment->id == $comment->id
                && $event->commentPoster->id == $poster->id
                && $event->profileOwner->id == $profileOwner->id;
            });
    }
}