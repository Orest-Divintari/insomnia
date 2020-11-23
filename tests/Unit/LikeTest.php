<?php

namespace Tests\Unit;

use App\Like;
use App\Reply;
use App\Thread;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\MessageFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_like_belongs_to_a_reply()
    {
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $like = Like::create([
            'user_id' => 1,
            'reply_id' => $reply->id,
        ]);

        $this->assertInstanceOf(Reply::class, $like->reply);

    }

    /** @test */
    public function a_like_has_activity()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply(
            raw(Reply::class, [
                'user_id' => $user->id,
            ]));

        $like = $reply->likedBy($user);

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
}