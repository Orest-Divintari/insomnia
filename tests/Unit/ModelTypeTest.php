<?php

namespace Tests\Unit;

use App\Helpers\ModelType;
use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_knows_the_type_of_a_thread_reply()
    {
        $reply = ReplyFactory::create();

        $this->assertEquals('reply', ModelType::get($reply));
    }

    /** @test */
    public function it_knows_the_type_of_a_profile_post_comment()
    {
        $comment = CommentFactory::create();

        $this->assertEquals('comment', ModelType::get($comment));
    }

    /** @test */
    public function it_knows_the_type_of_a_conversation_message()
    {
        $conversationStarer = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarer)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();

        $this->assertEquals('message', ModelType::get($message));
    }

    /** @test */
    public function it_knows_the_type_of_a_profile_post()
    {
        $profilePost = ProfilePostFactory::create();

        $this->assertEquals('profile-post', ModelType::get($profilePost));
    }

    /** @test */
    public function it_returns_the_type_of_a_profile_post_in_camel_case()
    {
        $profilePost = ProfilePostFactory::create();

        $this->assertEquals('profilePost', ModelType::toCamelCase($profilePost));
    }

    /** @test */
    public function it_knows_the_type_of_a_liked_thread_reply()
    {
        $reply = ReplyFactory::create();
        $user = create(User::class);
        $this->signIn($user);

        $like = $reply->like($user);

        $this->assertEquals('reply-like', ModelType::get($like));
    }

    /** @test */
    public function it_knows_the_type_of_a_liked_profile_post_comment()
    {
        $comment = CommentFactory::create();
        $user = create(User::class);
        $this->signIn($user);

        $like = $comment->like($user);

        $this->assertEquals('comment-like', ModelType::get($like));
    }

    /** @test */
    public function it_knows_the_type_of_a_liked_conversation_message()
    {
        $conversationStarer = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarer)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($participant);

        $like = $message->like($participant);

        $this->assertEquals('message-like', ModelType::get($like));
    }

    /** @test */
    public function it_knows_the_type_of_a_liked_profile_post()
    {
        $profilePost = ProfilePostFactory::create();
        $user = $this->signIn();
        $like = $profilePost->like($user);

        $this->assertEquals('profile-post-like', ModelType::get($like));
    }

    /** @test */
    public function it_appends_the_like_suffix_to_a_thread_reply()
    {
        $reply = ReplyFactory::create();

        $this->assertEquals('reply-like', ModelType::like($reply));
    }

    /** @test */
    public function it_appends_the_like_suffix_to_a_profile_post_comment()
    {
        $comment = CommentFactory::create();

        $this->assertEquals('comment-like', ModelType::like($comment));
    }

    /** @test */
    public function it_appends_the_like_suffix_to_a_conversation_message()
    {
        $conversationStarer = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarer)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();

        $this->assertEquals('message-like', ModelType::like($message));
    }

    /** @test */
    public function it_appends_the_like_suffix_to_a_profile_post()
    {
        $profilePost = ProfilePostFactory::create();

        $this->assertEquals('profile-post-like', ModelType::like($profilePost));
    }

}