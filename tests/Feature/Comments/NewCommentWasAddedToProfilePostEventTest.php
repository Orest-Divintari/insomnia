<?php

namespace Tests\Feature\Comments;

use App\Http\Middleware\ThrottlePosts;
use App\Listeners\Profile\NotifyPostParticipants;
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
    public function when_a_user_posts_a_comment_to_profile_post_then_an_event_is_fired()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $listener = Mockery::spy(NotifyPostParticipants::class);
        app()->instance(NotifyPostParticipants::class, $listener);

        $this->post(
            route('api.comments.store', $profilePost),
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