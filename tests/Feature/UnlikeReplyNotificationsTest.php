<?php

namespace Tests\Feature;

use App\Category;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Facades\Tests\Setup\ReplyFactory;

class UnlikeReplyNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
    }
    /** @test */
    public function when_a_user_unlikes_a_thread_reply_then_the_thread_reply_like_notification_is_deleted()
    {
        $poster = $this->signIn();
        $reply = ReplyFactory::by($poster)->create();
        $liker = $this->signIn();
        $like = $reply->likedBy($liker);
        $this->assertCount(1, $poster->notifications);

        $reply->unlikedBy($liker);

        $this->assertCount(0, $poster->fresh()->notifications);

        $reply->repliable->category->delete();
        $poster->delete();
        $liker->delete();
    }

    /** @test */
    public function when_a_user_unlikes_a_conversation_message_then_the_message_like_notification_is_deleted()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages->first();
        $this->signIn($participant);
        $like = $message->likedBy($participant);
        $this->assertCount(1, $conversationStarter->notifications);

        $message->unlikedBy($participant);

        $this->assertCount(0, $conversationStarter->fresh()->notifications);

        $conversation->delete();
        $message->delete();
        $conversationStarter->delete();
        $participant->delete();
    }

    /** @test */
    public function when_a_user_unlikes_a_profile_post_comment_then_then_the_comment_like_notification_is_deleted()
    {
        $poster = create(User::class);
        $comment = CommentFactory::by($poster)->create();
        $liker = $this->signIn();
        $comment->likedBy($liker);
        $this->assertCount(1, $poster->notifications);

        $comment->unlikedBy($liker);

        $this->assertCount(0, $poster->fresh()->notifications);

        $comment->repliable->delete();
        $comment->repliable->profileOwner->delete();
        $comment->repliable->poster->delete();
        $comment->delete();
        $liker->delete();
        $poster->delete();
    }
}