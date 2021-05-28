<?php

namespace Tests\Feature\Notifications;

use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Tests\TestCase;

class DeleteLikeNotificationsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
    }

    /** @test */
    public function when_user_unlikes_a_thread_reply_the_notification_is_deleted()
    {
        $poster = create(User::class);
        $reply = ReplyFactory::by($poster)->create();
        $liker = $this->signIn();
        $reply->likedBy($liker);
        $this->assertCount(1, $poster->notifications);

        $this->deleteJson(route('ajax.reply-likes.destroy', $reply));

        $this->assertCount(0, $poster->fresh()->notifications);

        $reply->delete();
        $poster->delete();
    }

    /** @test */
    public function when_user_unlikes_a_profile_post_comment_the_notification_is_deleted()
    {
        $poster = create(User::class);
        $comment = CommentFactory::by($poster)->create();
        $liker = $this->signIn();
        $comment->likedBy($liker);
        $this->assertCount(1, $poster->notifications);

        $this->deleteJson(route('ajax.reply-likes.destroy', $comment));

        $this->assertCount(0, $poster->fresh()->notifications);

        $comment->delete();
        $poster->delete();
    }

    /** @test */
    public function when_a_converastion_participant_unlikes_a_conversation_message_the_notification_is_deleted()
    {
        $this->withoutExceptionHandling();
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $message->likedBy($participant);
        $this->signIn($participant);
        $this->assertCount(1, $conversationStarter->notifications);

        $this->deleteJson(route('ajax.reply-likes.destroy', $message));

        $this->assertCount(0, $conversationStarter->fresh()->notifications);

        $message->delete();
        $conversation->delete();
        $conversationStarter->delete();
        $participant->delete();
    }

    /** @test */
    public function when_a_user_unlikes_a_profile_post_the_notification_is_deleted()
    {
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)->create();
        $liker = $this->signIn();
        $profilePost->likedBy($liker);
        $this->assertCount(1, $poster->notifications);

        $this->deleteJson(route('ajax.profile-post-likes.destroy', $profilePost));

        $this->assertCount(0, $poster->fresh()->notifications);

        $profilePost->delete();
        $liker->delete();
        $poster->delete();
    }
}