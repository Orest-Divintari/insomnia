<?php

namespace Tests\Feature\Notifications;

use App\Models\Category;
use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeleteLikeNotificationsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->useMysql();
    }

    /** @test */
    public function when_user_unlikes_a_thread_reply_the_notification_is_deleted()
    {
        $poster = create(User::class);
        $reply = ReplyFactory::by($poster)->create();
        $liker = $this->signIn();
        $reply->like($liker);
        $this->assertCount(1, $poster->notifications);

        $this->deleteJson(route('ajax.reply-likes.destroy', $reply));

        $this->assertCount(0, $poster->fresh()->notifications);

        $reply->repliable->category->delete();
        $reply->repliable->poster->delete();
        $liker->delete();
        $poster->delete();
    }

    /** @test */
    public function when_user_unlikes_a_profile_post_comment_the_notification_is_deleted()
    {
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)->toProfile($poster)->create();
        $comment = CommentFactory::by($poster)
            ->toProfilePost($profilePost)
            ->create();
        $liker = $this->signIn();
        $comment->like($liker);
        $this->assertCount(1, $poster->notifications);

        $this->deleteJson(route('ajax.reply-likes.destroy', $comment));

        $this->assertCount(0, $poster->fresh()->notifications);

        $profilePost->delete();
        $poster->delete();
        $liker->delete();
    }

    /** @test */
    public function when_a_conversation_participant_unlikes_a_conversation_message_the_notification_is_deleted()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($participant);
        $message->like($participant);
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
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($poster)
            ->create();
        $liker = $this->signIn();
        $profilePost->like($liker);
        $this->assertCount(1, $poster->notifications);

        $this->deleteJson(route('ajax.profile-post-likes.destroy', $profilePost));

        $this->assertCount(0, $poster->fresh()->notifications);

        $profilePost->delete();
        $liker->delete();
        $poster->delete();
    }
}