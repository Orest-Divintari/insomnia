<?php

namespace Tests\Feature;

use App\Activity;
use App\Like;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\MessageFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signIn();
    }

    /** @test */
    public function creating_a_thread_records_activty()
    {
        $thread = create(Thread::class);
        $this->assertCount(1, $thread->activity);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
        ]);
    }

    /** @test */
    public function creating_a_thread_reply_records_activity()
    {
        $threadReply = ReplyFactory::create();
        $this->assertCount(1, $threadReply->activity);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $threadReply->id,
            'subject_type' => Reply::class,
        ]);
    }

    /** @test */
    public function creating_the_first_reply_which_is_the_body_of_the_thread_should_no_record_activity()
    {
        $thread = create(Thread::class);
        $firstReply = $thread->replies()->first();
        $this->assertCount(0, $firstReply->activity);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $firstReply->id,
            'subject_type' => Reply::class,
        ]);
    }

    /** @test */
    public function creating_a_conversation_message_should_no_record_activity()
    {
        $message = MessageFactory::create();
        $this->assertCount(0, $message->activity);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $message->id,
            'subject_type' => Reply::class,
        ]);
    }

    /** @test */
    public function creating_a_profile_post_records_activity()
    {
        $profilePost = create(ProfilePost::class);
        $this->assertCount(1, $profilePost->activity);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
        ]);
    }

    /** @test */
    public function creating_a_profile_post_comment_records_activity()
    {
        $comment = CommentFactory::create();
        $this->assertCount(1, $comment->activity);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
        ]);
    }

    /** @test */
    public function liking_a_thread_reply_records_activity()
    {
        $threadReply = ReplyFactory::create();
        $threadReply->likedBy();
        $like = $threadReply->likes()->first();
        $this->assertCount(1, $like->activity);
        $this->assertDatabaseHas('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
        ]);
    }

    /** @test */
    public function liking_a_conversation_message_should_not_record_activity()
    {
        $message = MessageFactory::create();
        $like = $message->likedBy();
        $this->assertCount(0, $like->activity);
        $this->assertDatabaseMissing('activities', [
            'subject_id' => $like->id,
            'subject_type' => Like::class,
        ]);
    }

}