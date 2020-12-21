<?php

namespace Tests\Feature\Comments;

use App\Like;
use App\Listeners\Profile\NotifyCommentPoster;
use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CommentWasLikedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_likes_a_profile_post_comment_then_an_event_is_fired()
    {
        $listener = Mockery::spy(NotifyCommentPoster::class);
        app()->instance(NotifyCommentPoster::class, $listener);
        $profileOwner = create(User::class);
        $profilePost = create(ProfilePost::class, ['profile_owner_id' => $profileOwner->id]);
        $commentPoster = create(User::class);
        $comment = $profilePost->addComment([
            'body' => 'some comment',
            'user_id' => $commentPoster->id,
        ],
            $commentPoster
        );
        $liker = $this->signIn();

        $this->post(route('api.likes.store', $comment));

        $like = Like::where('reply_id', $comment->id)->first();
        $listener->shouldHaveReceived('handle', function ($event) use (
            $like,
            $comment,
            $profilePost,
            $profileOwner,
            $commentPoster,
            $liker
        ) {
            return $event->comment->id == $comment->id
            && $event->commentPoster->id == $commentPoster->id
            && $event->liker->id == $liker->id
            && $event->like->id == $like->id
            && $event->profilePost->id == $profilePost->id
            && $event->profileOwner->id == $profileOwner->id;
        });

    }
}