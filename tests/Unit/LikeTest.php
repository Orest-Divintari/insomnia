<?php

namespace Tests\Unit;

use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\MessageFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_like_belongs_to_a_likeable_model()
    {
        $thread = create(Thread::class);
        $reply = ReplyFactory::toThread($thread)->create();
        $liker = create(User::class);

        $like = $reply->likedBy($liker);

        $this->assertInstanceOf(Reply::class, $like->likeable);
    }

    /** @test */
    public function a_like_has_activity()
    {
        $user = $this->signIn();
        $threadReply = ReplyFactory::create();

        $like = $threadReply->likedBy($user);

        $this->assertCount(1, $like->activities);
    }

    /** @test */
    public function a_thread_reply_like_should_be_recordable()
    {
        $this->signIn();
        $threadReply = ReplyFactory::create();

        $like = $threadReply->likedBy();

        $this->assertTrue($like->shouldBeRecordable());
    }

    /** @test */
    public function a_profile_post_comment_like_should_be_recordable()
    {
        $this->signIn();
        $comment = CommentFactory::create();

        $like = $comment->likedBy();

        $this->assertTrue($like->shouldBeRecordable());
    }

    /** @test */
    public function a_conversation_message_like_should_not_be_recordable()
    {
        $this->signIn();
        $message = MessageFactory::create();

        $like = $message->likedBy();

        $this->assertFalse($like->shouldBeRecordable());
    }

    /** @test */
    public function it_knows_who_created_the_like()
    {
        $liker = $this->signIn();
        $profilePost = ProfilePostFactory::create();
        $like = $profilePost->likedBy($liker);

        $this->assertEquals($liker->id, $like->liker->id);
    }

}