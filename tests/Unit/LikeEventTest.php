<?php

namespace Tests\Unit;

use App\Events\Conversation\MessageWasLiked;
use App\Events\LikeEvent;
use App\Events\Profile\CommentWasLiked;
use App\Events\Subscription\ReplyWasLiked;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;
use \Facades\Tests\Setup\MessageFactory;

class LikeEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_thread_reply_is_liked_then_create_a_thread_rely_was_liked_event()
    {
        $reply = ReplyFactory::create();
        $liker = $this->signIn();
        $like = $reply->likedBy($liker);

        $likeEvent = (new LikeEvent($liker, $reply, $like))->create();

        $this->assertInstanceOf(ReplyWasLiked::class, $likeEvent);
    }

    /** @test */
    public function when_a_profile_post_comment_is_liked_then_create_a_comment_was_liked_event()
    {
        $comment = CommentFactory::create();
        $liker = $this->signIn();
        $like = $comment->likedBy($liker);

        $likeEvent = (new LikeEvent($liker, $comment, $like))->create();

        $this->assertInstanceOf(CommentWasLiked::class, $likeEvent);
    }

    /** @test */
    public function when_a_conversation_message_is_liked_then_create_a_message_was_liked_event()
    {
        $message = MessageFactory::create();
        $liker = $this->signIn();
        $like = $message->likedBy($liker);

        $likeEvent = (new LikeEvent($liker, $message, $like))->create();

        $this->assertInstanceOf(MessageWasLiked::class, $likeEvent);
    }
}