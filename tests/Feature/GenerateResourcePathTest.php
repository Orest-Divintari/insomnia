<?php

namespace Tests\Feature;

use App\Conversation;
use App\Helpers\Facades\ResourcePath;
use App\ProfilePost;
use App\Thread;
use App\User;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Facades\Tests\Setup\CommentFactory;
use \Facades\Tests\Setup\MessageFactory;
use \Facades\Tests\Setup\ProfilePostFactory;

class GenerateResourcePathTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_the_path_for_a_thread_reply()
    {
        $thread = create(Thread::class);
        $numberOfPages = 5;
        $replies = ReplyFactory::toThread($thread)
            ->createMany(Thread::REPLIES_PER_PAGE * $numberOfPages);
        $lastReply = $replies->last();

        $this->assertEquals(
            route('threads.show', $thread) .
            '?page=' . $numberOfPages .
            '#post-' . $lastReply->id,
            ResourcePath::generate($lastReply)
        );
    }

    /** @test */
    public function it_generates_the_path_for_a_conversation_message()
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
            ResourcePath::generate($message)
        );

    }

    /** @test */
    public function it_generates_the_path_for_a_profile_post_comment()
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
            '#profile-post-comment-' . $comment->id,
            ResourcePath::generate($comment)
        );
    }

    /** @test */
    public function it_generates_the_path_for_a_profile_post()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $numberOfPages = 5;
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * $numberOfPages);

        $lastPost = $posts->last();

        $this->assertEquals(
            route('profiles.show', $orestis) .
            '?page=' . $numberOfPages .
            '#profile-post-' . $lastPost->id,
            ResourcePath::generate($lastPost)
        );
    }

    /** @test */
    public function it_generates_the_page_number_that_a_profile_post_comment_belongs_to()
    {
        $profilePost = create(ProfilePost::class);
        $numberOfPages = 5;
        $comments = CommentFactory::toProfilePost($profilePost)
            ->createMany(ProfilePost::REPLIES_PER_PAGE * $numberOfPages);

        $comment = $comments->last();

        $this->assertEquals($numberOfPages, ResourcePath::pageNumber($comment));
    }

    /** @test */
    public function it_generates_the_page_number_that_a_thread_reply_belongs_to()
    {
        $thread = create(Thread::class);
        $numberOfPages = 5;
        $threadReplies = ReplyFactory::toThread($thread)->createMany(Thread::REPLIES_PER_PAGE * $numberOfPages);
        $reply = $threadReplies->last();

        $this->assertEquals($numberOfPages, ResourcePath::pageNumber($reply));
    }

    /** @test */
    public function it_generates_the_page_number_that_a_profile_post_belongs_to()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $numberOfPages = 5;
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * $numberOfPages);

        $lastPost = $posts->last();

        $this->assertEquals($numberOfPages, ResourcePath::pageNumber($lastPost));
    }

    /** @test */
    public function it_generates_the_page_number_that_a_message_belongs_to()
    {
        $conversation = create(Conversation::class);
        $numberOfPages = 5;
        $messages = MessageFactory::toConversation($conversation)
            ->createMany(Conversation::REPLIES_PER_PAGE * $numberOfPages);

        $message = $messages->last();

        $this->assertEquals($numberOfPages, ResourcePath::pageNumber($message));
    }
}