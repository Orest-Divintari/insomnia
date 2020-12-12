<?php

namespace Tests\Unit;

use App\Activity;
use App\Filters\ReplyFilters;
use App\Like;
use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\MessageFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{

    use RefreshDatabase;
    /** @test */
    public function a_reply_belongs_to_a_thread()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['repliable_id' => $thread->id, 'repliable_type' => Thread::class]);

        $this->assertInstanceOf(Thread::class, $reply->repliable);
    }

    /** @test */
    public function a_reply_belongs_to_the_user_who_posted_it()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $reply = create(Reply::class, [
            'user_id' => $user->id,
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->assertInstanceOf(User::class, $reply->fresh()->poster);
        $this->assertEquals($user->id, $reply->poster->id);

    }

    /** @test */
    public function a_reply_may_have_likes()
    {
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        Like::create([
            'reply_id' => $reply->id,
            'user_id' => 1,
        ]);

        $this->assertCount(1, $reply->likes);
    }

    /** @test */
    public function a_reply_can_be_liked_by_a_user()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $reply->likedBy($user);

        $this->assertCount(1, $reply->fresh()->likes);

    }

    /** @test */
    public function a_reply_can_be_unliked_by_a_user()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $reply->likedBy($user);

        $this->assertCount(1, $reply->fresh()->likes);

        $reply->unlikedBy($user);

        $this->assertCount(0, $reply->fresh()->likes);

    }

    /** @test */
    public function a_thread_reply_knows_in_which_page_it_belongs_to()
    {
        ReplyFactory::createMany(50);

        $reply = Reply::find(15);
        $correctPageNumber = ceil(15 / $reply->repliable::REPLIES_PER_PAGE);

        $this->assertEquals($correctPageNumber, $reply->pageNumber);
    }

    /** @test */
    public function a_profile_post_comment_knows_in_which_page_it_belongs_to()
    {
        CommentFactory::createMany(50);

        $comment = Reply::find(15);
        $correctPageNumber = ceil(15 / $comment->repliable::REPLIES_PER_PAGE);

        $this->assertEquals($correctPageNumber, $comment->pageNumber);
    }

    /** @test */
    public function a_conversation_message_knows_in_which_page_it_belongs_to()
    {
        MessageFactory::createMany(50);

        $message = Reply::find(15);

        $correctPageNumber = ceil(15 / $message->repliable::REPLIES_PER_PAGE);

        $this->assertEquals($correctPageNumber, $message->pageNumber);
    }

    /** @test */
    public function a_like_knows_if_it_is_liked_by_the_authenticated_user()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $reply = $thread->addReply(raw(Reply::class));

        $this->assertFalse($reply->fresh()->is_liked);

        $reply->likedBy($user);

        $this->assertTrue($reply->fresh()->is_liked);
    }

    /** @test */
    public function every_reply_that_belongs_to_a_thread_has_a_position_in_ascending_order()
    {
        $thread = create(Thread::class);

        $firstReply = $thread->addReply(raw(Reply::class));

        $secondReply = $thread->addReply(raw(Reply::class));

        $anotherThread = create(Thread::class);

        $replyOnAnotherThread = $anotherThread->addReply(raw(Reply::class));

        $this->assertEquals(2, $firstReply->fresh()->position);
        $this->assertEquals(3, $secondReply->fresh()->position);
        $this->assertEquals(2, $replyOnAnotherThread->fresh()->position);
    }

    /** @test */
    public function a_reply_has_activities()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class, ['user_id' => $user->id]));

        $this->assertCount(1, $reply->activities);
        $this->assertEquals(
            $reply->id,
            $reply->activities->first()
                ->subject->id
        );
    }

    /** @test */
    public function a_reply_can_eager_load_the_data_required_to_display_when_a_user_searches_a_reply()
    {
        ReplyFactory::create();
        $reply = Reply::withSearchInfo()->first();
        $replyArray = $reply->toArray();

        $this->assertArrayHasKey('poster', $replyArray);
        $this->assertEquals($reply->poster->id, $replyArray['poster']['id']);

        $this->assertArrayHasKey('repliable', $replyArray);
        $this->assertEquals($reply->repliable->id, $replyArray['repliable']['id']);

        $this->assertArrayHasKey('poster', $reply['repliable']);
        $this->assertEquals($reply->repliable->poster->id, $replyArray['repliable']['poster']['id']);

        $this->assertEquals($reply->repliable->category->id, $replyArray['repliable']['category']['id']);
        $this->assertArrayHasKey('category', $reply['repliable']);
    }

    /** @test */
    public function a_reply_knows_if_it_is_a_thread_reply()
    {
        $reply = ReplyFactory::create();
        $this->assertEquals($reply->type, 'thread-reply');
    }

    /** @test */
    public function a_reply_knows_if_it_is_a_profile_post_comment()
    {
        $comment = CommentFactory::create();
        $this->assertEquals($comment->type, 'profile-post-comment');
    }

    /** @test */
    public function a_reply_knows_if_it_is_a_conversation_message()
    {
        $message = MessageFactory::create();
        $this->assertEquals($message->type, 'conversation-message');
    }

    /** @test */
    public function a_reply_is_sanitized_automatically()
    {
        $reply = ReplyFactory::create([
            'body' =>
            '<script>alert("bad")</script><p>This is okay.</p>',
        ]);

        $this->assertEquals("<p>This is okay.</p>", $reply->body);
    }

    /** @test */
    public function get_the_paginated_replies_with_likes_and_filtered_for_a_specific_thread()
    {
        $thread = create(Thread::class);
        $replies = ReplyFactory::createMany(
            Reply::REPLIES_PER_PAGE * 2,
            ['repliable_id' => $thread->id]
        );

        $reply = $replies->first();
        $user = $this->signIn();
        $reply->likedBy($user);

        $replyFilters = app(ReplyFilters::class);

        $paginatedReplies = Reply::forThread($thread, $replyFilters);
        $replyArray = $paginatedReplies->firstWhere('id', $reply->id);

        $this->assertEquals(
            1,
            $paginatedReplies
                ->toArray()['current_page']
        );
        $this->assertArrayHasKey(
            'is_liked',
            $replyArray
        );
        $this->assertTrue($replyArray['is_liked']);
        $this->assertEquals(
            $replyArray['repliable_id'],
            $reply->repliable->id
        );
    }

    /** @test */
    public function a_profile_post_comment_activity_should_be_recordable()
    {
        $this->signIn();
        $comment = CommentFactory::create();
        $this->assertTrue($comment->shouldBeRecordable());
        $this->assertCount(1, $comment->activity);
    }

    /** @test */
    public function a_thread_reply_should_be_recordable()
    {
        $reply = ReplyFactory::create();
        $this->assertTrue($reply->shouldBeRecordable());
    }

    /** @test */
    public function the_first_reply_of_a_thread_which_consists_of_the_body_of_a_thread_should_not_be_recordable()
    {
        $firstReply = create(Thread::class)->replies()->first();
        $this->assertFalse($firstReply->shouldBeRecordable());
    }

    /** @test */
    public function a_conversation_message_should_not_be_recordable()
    {
        $message = MessageFactory::create();
        $this->assertFalse($message->shouldBeRecordable());
    }
}