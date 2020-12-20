<?php

namespace Tests\Feature;

use App\Listeners\Like\DeleteReplyLikeNotification;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;
use \Facades\Tests\Setup\MessageFactory;

class ReplyWasUnlikedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_thread_reply_is_unliked_then_an_event_is_fired()
    {
        $listener = Mockery::spy(DeleteReplyLikeNotification::class);
        app()->instance(DeleteReplyLikeNotification::class, $listener);
        $reply = ReplyFactory::create();
        $liker = $this->signIn();
        $like = $reply->likedBy($liker);

        $reply->unlikedBy($liker);

        $listener->shouldHaveReceived('handle', function ($event) use ($like, $reply) {
            return $event->likeId == $like->id &&
            $event->reply->id == $reply->id;
        });
    }

    /** @test */
    public function when_a_conversation_message_is_unliked_then_an_event_is_fired()
    {
        $listener = Mockery::spy(DeleteReplyLikeNotification::class);
        app()->instance(DeleteReplyLikeNotification::class, $listener);
        $message = MessageFactory::create();
        $liker = $this->signIn();
        $like = $message->likedBy($liker);

        $message->unlikedBy($liker);

        $listener->shouldHaveReceived('handle', function ($event) use ($like, $message) {
            return $event->likeId == $like->id &&
            $event->reply->id == $message->id;
        });
    }

    /** @test */
    public function when_a_profile_post_comment_is_unliked_then_an_event_is_fired()
    {
        $listener = Mockery::spy(DeleteReplyLikeNotification::class);
        app()->instance(DeleteReplyLikeNotification::class, $listener);
        $comment = CommentFactory::create();
        $liker = $this->signIn();
        $like = $comment->likedBy($liker);

        $comment->unlikedBy($liker);

        $listener->shouldHaveReceived('handle', function ($event) use ($like, $comment) {
            return $event->likeId == $like->id &&
            $event->reply->id == $comment->id;
        });
    }
}