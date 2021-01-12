<?php

namespace Tests\Unit;

use App\Activity;
use App\Conversation;
use App\Filters\ReplyFilters;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
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
        $reply = ReplyFactory::toThread($thread)->create();

        $this->assertInstanceOf(Thread::class, $reply->repliable);
    }

    /** @test */
    public function a_reply_belongs_to_the_user_who_posted_it()
    {
        $user = create(User::class);

        $reply = ReplyFactory::by($user)->create();

        $this->assertInstanceOf(User::class, $reply->fresh()->poster);
        $this->assertEquals($user->id, $reply->poster->id);

    }

    /** @test */
    public function a_reply_may_have_likes()
    {
        $reply = ReplyFactory::create();
        $user = create(User::class);

        $reply->likedBy($user);

        $this->assertCount(1, $reply->likes);
    }

    /** @test */
    public function a_reply_can_be_unliked_by_a_user()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $reply = ReplyFactory::create();
        $user = create(User::class);
        $reply->likedBy($user);
        $this->assertCount(1, $reply->likes);

        $reply->unlikedBy($user);

        $this->assertCount(0, $reply->fresh()->likes);
    }

    /** @test */
    public function a_thread_reply_knows_on_which_page_it_belongs_to()
    {
        $thread = create(Thread::class);
        ReplyFactory::toThread($thread)->createMany(50);
        $reply = Reply::find(15);

        $correctPageNumber = ceil(15 / $reply->repliable::REPLIES_PER_PAGE);

        $this->assertEquals($correctPageNumber, $reply->pageNumber);
    }

    /** @test */
    public function a_profile_post_comment_knows_on_which_page_it_belongs_to()
    {
        $profilePost = create(ProfilePost::class);
        CommentFactory::toProfilePost($profilePost)->createMany(50);

        $comment = Reply::find(15);
        $correctPageNumber = ceil(15 / $comment->repliable::REPLIES_PER_PAGE);

        $this->assertEquals($correctPageNumber, $comment->pageNumber);
    }

    /** @test */
    public function a_conversation_message_knows_on_which_page_it_belongs_to()
    {
        $conversation = create(Conversation::class);
        MessageFactory::toConversation($conversation)->createMany(50);

        $message = Reply::find(15);
        $correctPageNumber = ceil(15 / $message->repliable::REPLIES_PER_PAGE);

        $this->assertEquals($correctPageNumber, $message->pageNumber);
    }

    /** @test */
    public function a_thread_reply_knows_if_it_is_liked_by_the_authenticated_user()
    {
        $user = $this->signIn();
        $threadReply = ReplyFactory::create();
        $this->assertFalse($threadReply->isLiked);

        $threadReply->likedBy($user);

        $this->assertTrue($threadReply->fresh()->isLiked);
    }

    /** @test */
    public function every_reply_that_belongs_to_a_thread_has_an_incrementing_position()
    {
        $thread = create(Thread::class);
        $firstReply = ReplyFactory::toThread($thread)->create();
        $secondReply = ReplyFactory::toThread($thread)->create();

        $this->assertEquals(2, $firstReply->fresh()->position);
        $this->assertEquals(3, $secondReply->fresh()->position);
    }

    /** @test */
    public function when_a_thread_reply_is_created_the_replies_count_of_a_the_thread_increments()
    {
        $thread = create(Thread::class);
        $this->assertEquals(0, $thread->replies_count);

        $firstReply = ReplyFactory::toThread($thread)->create();

        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    public function a_reply_knows_if_it_is_the_body_of_a_thread()
    {
        $thread = create(Thread::class);
        $reply = $thread->replies()->first();

        $this->assertTrue($reply->isThreadBody());
    }
    /** @test */
    public function the_reply_that_consists_as_a_body_of_the_thread_does_not_count_as_a_reply()
    {
        $thread = create(Thread::class);

        $this->assertCount(1, Reply::all());

        $this->assertFalse($thread->hasReplies());
    }

    /** @test */
    public function a_reply_has_activities()
    {
        $user = $this->signIn();
        $threadReply = ReplyFactory::by($user)->create();

        $this->assertCount(1, $threadReply->activities);
        $this->assertEquals(
            $threadReply->id,
            $threadReply->activities->first()
                ->subject->id
        );
    }

    /** @test */
    public function the_thread_reply_that_consists_of_the_body_of_the_thread_should_not_be_searchable()
    {
        $thread = create(Thread::class);
        $reply = $thread->replies()->first();

        $this->assertFalse($reply->shouldBeSearchable());
    }

    /** @test */
    public function a_thread_reply_should_be_searchable()
    {
        $threadReply = ReplyFactory::create();

        $this->assertTrue($threadReply->shouldBeSearchable());
    }

    /** @test */
    public function a_comment_should_be_searchable()
    {
        $comment = CommentFactory::create();

        $this->assertTrue($comment->shouldBeSearchable());
    }

    /** @test */
    public function a_conversation_message_should_not_be_searchable()
    {
        $message = MessageFactory::create();

        $this->assertFalse($message->shouldBeSearchable());
    }

    /** @test */
    public function a_reply_can_eager_load_the_data_required_data_when_it_is_searched()
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
        $reply = ReplyFactory::withBody('<script>alert("bad")</script><p>This is okay.</p>')->create();

        $this->assertEquals("<p>This is okay.</p>", $reply->body);
    }

    /** @test */
    public function get_the_paginated_replies_with_likes_and_filtered_for_a_specific_thread()
    {
        $thread = create(Thread::class);
        $replies = ReplyFactory::toThread($thread)
            ->createMany(Thread::REPLIES_PER_PAGE * 2);
        $reply = $replies->first();
        $user = $this->signIn();
        $reply->likedBy($user);
        $replyFilters = app(ReplyFilters::class);

        $paginatedReplies = Reply::forRepliable($thread, $replyFilters);
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

    /** @test */
    public function a_reply_knows_if_it_is_for_thread()
    {
        $reply = ReplyFactory::create();

        $this->assertTrue($reply->isThreadReply());
    }

    /** @test */
    public function a_reply_knows_if_it_is_for_profile_post()
    {
        $comment = CommentFactory::create();

        $this->assertTrue($comment->isComment());
    }

    /** @test */
    public function a_reply_knows_if_it_is_for_conversation_message()
    {
        $this->signIn();
        $conversation = ConversationFactory::create();
        $message = $conversation->messages->first();

        $this->assertTrue($message->isMessage());
    }

}