<?php

namespace Tests\Feature\Comments;

use App\Listeners\Profile\NotifyPostParticipants;
use App\ProfilePost;
use App\Reply;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NewCommentWasAddedToProfilePostEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_posts_a_comment_to_profile_post_then_an_event_is_fired()
    {
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );
        $comment = ['body' => 'some comment'];
        $listener = Mockery::spy(NotifyPostParticipants::class);
        app()->instance(NotifyPostParticipants::class, $listener);

        $this->post(
            route('api.comments.store', $profilePost),
            ['body' => $comment['body']]
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