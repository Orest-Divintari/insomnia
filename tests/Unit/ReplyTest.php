<?php

namespace Tests\Unit;

use App\Filters\ReplyFilters;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\MessageFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{

    use RefreshDatabase;
    /** @test */
    public function a_reply_belongs_to_a_thread()
    {
        $thread = create('App\Models\Thread');
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

        $reply->like($user);

        $this->assertCount(1, $reply->likes);
    }

    /** @test */
    public function a_reply_can_be_unliked_by_a_user()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $reply = ReplyFactory::create();
        $user = create(User::class);
        $reply->like($user);

        $this->assertCount(1, $reply->likes);

        $reply->unlike($user);

        $this->assertCount(0, $reply->fresh()->likes);

        $reply->repliable->category->delete();
        $reply->repliable->poster->delete();
        $reply->poster->delete();
        $user->delete();
    }

    /** @test */
    public function a_profile_post_comment_knows_its_path()
    {
        $orestis = create(User::class);
        $numberOfPages = 5;
        $posts = ProfilePostFactory::toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * $numberOfPages);
        $lastPost = $posts->last();
        $comment = CommentFactory::toProfilePost($lastPost)->create();

        $this->assertEquals(
            route('profiles.show', $orestis) .
            "?page=" . $numberOfPages .
            '#profile-post-' . $lastPost->id,
            $comment->path
        );
    }

    /** @test */
    public function a_conversation_message_knows_its_path()
    {
        $conversation = create(Conversation::class);
        $numberOfPages = 5;
        $messages = MessageFactory::toConversation($conversation)
            ->createMany(Conversation::REPLIES_PER_PAGE * $numberOfPages);

        $message = $messages->last();

        $this->assertEquals(
            route('conversations.show', $message->repliable) .
            "?page=" . $numberOfPages .
            '#convMessage-' . $message->id,
            $message->path
        );
    }

    /** @test */
    public function a_thread_reply_knows_its_path()
    {
        $thread = create(Thread::class);
        $numberOfPages = 5;
        $expectedPageNumber = 6;
        $replies = ReplyFactory::toThread($thread)
            ->createMany(Thread::REPLIES_PER_PAGE * $numberOfPages);
        $lastReply = $replies->last();

        $this->assertEquals(
            route('threads.show', $thread) .
            '?page=' . $expectedPageNumber .
            '#post-' . $lastReply->id,
            $lastReply->path
        );
    }

    /** @test */
    public function a_thread_reply_knows_if_it_is_liked_by_the_authenticated_user()
    {
        $user = $this->signIn();
        $threadReply = ReplyFactory::create();

        $threadReply->like($user);

        $threadReply = Reply::whereId($threadReply->id)
            ->withIsLikedByAuthUser()
            ->first();

        $this->assertTrue($threadReply->is_liked);
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
        $reply->like($user);
        $replyFilters = app(ReplyFilters::class);

        $paginatedReplies = Reply::withLikes()->filter($replyFilters)->paginate(Thread::REPLIES_PER_PAGE);
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

    /** @test */
    public function it_associates_a_user_to_itself()
    {
        $reply = ReplyFactory::create();
        $user = create(User::class);
        $this->assertNotEquals($reply->poster->id, $user->id);

        $reply->setPoster($user)->save();

        $this->assertEquals($reply->fresh()->poster->id, $user->id);
    }

    /** @test */
    public function it_knows_if_it_is_liked()
    {
        $reply = ReplyFactory::create();
        $user = create(User::class);

        $reply->like($user);

        $this->assertTrue($reply->isLiked($user));
    }

    /** @test */
    public function it_wraps_mentioned_names_with_anchor_tags()
    {
        $reply = new Reply([
            'body' => 'Hello @Jane-Doe.',
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.',
            $reply->body
        );
    }

    /** @test */
    public function it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = new Reply([
            'body' => '@JaneDoe wants to talk to @JohnDoe',
        ]);

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());
    }

}