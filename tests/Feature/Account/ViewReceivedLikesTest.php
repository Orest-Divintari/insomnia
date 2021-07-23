<?php

namespace Tests\Feature\Accoubt;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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
        $profilePost->like($liker);
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
        Notification::fake();
        $poster = create(User::class);
        $reply = ReplyFactory::by($poster)->create();
        $liker = $this->signIn();
        $reply->like($liker);
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
        $comment->like($liker);
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
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($participant);
        $message->like($participant);
        $this->signIn($conversationStarter);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }

    /** @test */
    public function users_should_not_view_their_own_likes_on_their_profile_posts()
    {
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)->create();
        $profilePost->like($poster);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }

    /** @test */
    public function users_should_not_view_their_own_likes_on_their_own_thread_replies()
    {
        $poster = create(User::class);
        $reply = ReplyFactory::by($poster)->create();
        $reply->like($poster);
        $this->signIn($poster);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }

    /** @test */
    public function users_should_not_view_their_own_likes_on_their_own_profile_post_comments()
    {
        $poster = $this->signIn();
        $comment = CommentFactory::by($poster)->create();
        $comment->like($poster);

        $response = $this->get(route('account.likes.index'));

        $this->assertEmpty($response['likes']);
    }
}
