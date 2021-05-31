<?php

namespace Tests\Feature\Accoubt;

use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewReceivedLikesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_may_view_the_likes_their_profile_posts_received()
    {
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)->create();
        $liker = $this->signIn();
        $profilePost->likedBy($liker);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $like = $response['likes'][0];
        $this->assertArrayHasKey('likes', $response);
        $this->assertEquals($like['liker']['id'], $liker->id);
        $this->assertEquals($like['likeable']['id'], $profilePost->id);
        $this->assertEquals($like['likeable_type'], ProfilePost::class);
        $this->assertEquals($like['likeable']['profileOwner']['id'], $profilePost->profileOwner->id);
    }

    /** @test */
    public function users_may_view_the_likes_their_thread_replies_have_received()
    {
        $poster = create(User::class);
        $reply = ReplyFactory::by($poster)->create();
        $liker = $this->signIn();
        $reply->likedBy($liker);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $like = $response['likes'][0];
        $this->assertArrayHasKey('likes', $response);
        $this->assertEquals($like['likeable_type'], Reply::class);
        $this->assertEquals($like['liker']['id'], $liker->id);
        $this->assertEquals($like['likeable']['id'], $reply->id);
        $this->assertEquals($like['likeable']['repliable']['id'], $reply->repliable->id);
        $this->assertEquals($like['likeable']['repliable_type'], Thread::class);
    }

    /** @test */
    public function users_may_view_the_likes_their_profile_post_comments_have_received()
    {
        $poster = create(User::class);
        $comment = CommentFactory::by($poster)->create();
        $liker = $this->signIn();
        $comment->likedBy($liker);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $like = $response['likes'][0];
        $this->assertArrayHasKey('likes', $response);
        $this->assertEquals($like['likeable_type'], Reply::class);
        $this->assertEquals($like['liker']['id'], $liker->id);
        $this->assertEquals($like['likeable']['id'], $comment->id);
        $this->assertEquals($like['likeable']['repliable']['id'], $comment->repliable->id);
        $this->assertEquals($like['likeable']['repliable_type'], ProfilePost::class);
    }

    /** @test */
    public function users_should_not_view_the_likes_their_conversation_messages_have_received()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $message->likedBy($participant);
        $this->signIn($conversationStarter);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }

    /** @test */
    public function users_should_not_view_their_own_likes_on_their_profile_posts()
    {
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)->create();
        $profilePost->likedBy($poster);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }

    /** @test */
    public function users_should_not_view_their_own_likes_on_their_own_thread_replies()
    {
        $poster = create(User::class);
        $reply = ReplyFactory::by($poster)->create();
        $reply->likedBy($poster);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }

    /** @test */
    public function users_should_not_view_their_own_likes_on_their_own_profile_post_comments()
    {
        $poster = create(User::class);
        $comment = CommentFactory::by($poster)->create();
        $comment->likedBy($poster);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }
}