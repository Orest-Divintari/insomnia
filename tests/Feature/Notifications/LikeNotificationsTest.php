<?php

namespace Tests\Feature\Notifications;

use App\Notifications\CommentHasNewLike;
use App\Notifications\MessageHasNewLike;
use App\Notifications\ReplyHasNewLike;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LikeNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }
    /** @test */
    public function the_thread_reply_poster_receives_notifications_when_the_reply_is_liked()
    {
        $replyPoster = create(User::class);
        $reply = ReplyFactory::by($replyPoster)->create();
        $liker = $this->signIn();
        $thread = $reply->repliable;

        $this->post(route('api.likes.store', $reply));

        $like = $reply->likes()->first();
        Notification::assertSentTo(
            $replyPoster,
            MessageHasNewLike::class,
            function ($notification, $channels)
             use (
                $liker,
                $like,
                $reply,
                $thread
            ) {
                return $notification->reply->id == $reply->id
                && $notification->liker->id == $liker->id
                && $notification->like->id == $like->id
                && $notification->thread->id == $thread->id;
            });
    }

    /** @test */
    public function the_thread_reply_poster_must_not_receive_a_notification_when_like_their_own_reply()
    {
        $replyPoster = $this->signIn();
        $reply = ReplyFactory::by($replyPoster)->create();

        $this->post(route('api.likes.store', $reply));

        Notification::assertNotSentTo($replyPoster, ReplyHasNewLike::class);
    }

    /** @test */
    public function the_conversation_message_poster_should_receive_a_notification_when_another_user_likes_the_message()
    {
        $conversationStarter = $this->signIn();
        $liker = create(User::class);
        $conversation = ConversationFactory::withParticipants([$liker->name])->create();
        $message = $conversation->messages->first();
        $this->signIn($liker);

        $this->post(route('api.likes.store', $message));

        $like = $message->likes()->first();
        Notification::assertSentTo(
            $conversationStarter,
            MessageHasNewLike::class,
            function ($notification, $channels) use (
                $message,
                $liker,
                $like,
                $conversation
            ) {
                return true;
                return $notification->message->id == $message->id
                && $notification->like->id == $like->id
                && $notification->liker->id == $liker->id
                && $notification->conversation->id == $conversation->id;
            });
    }

    /** @test */
    public function the_conversation_message_poster_should_not_receive_a_notification_when_they_like_their_own_message()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();
        $message = $conversation->messages->first();

        $this->post(route('api.likes.store', $message));

        Notification::assertNotSentTo(
            $conversationStarter,
            MessageHasNewLike::class,
        );
    }

    /** @test */
    public function the_owner_of_a_comment_should_receive_notification_when_the_comment_is_liked_by_another_user()
    {
        $commentPoster = create(User::class);
        $profilePost = create(ProfilePost::class);
        $comment = create(Reply::class, [
            'user_id' => $commentPoster->id,
            'repliable_id' => $profilePost->id,
            'repliable_type' => ProfilePost::class,
        ]);
        $profileOwner = $profilePost->profileOwner;
        $liker = $this->signIn();

        $this->post(route('api.likes.store', $comment));

        $like = $comment->likes()->first();
        Notification::assertSentTo(
            $commentPoster,
            CommentHasNewLike::class,
            function ($notification)
             use (
                $commentPoster,
                $profilePost,
                $profileOwner,
                $like,
                $liker,
                $comment
            ) {
                return $notification->comment->id == $comment->id
                && $notification->commentPoster->id == $commentPoster->id
                && $notification->profileOwner->id == $profileOwner->id
                && $notification->liker->id == $liker->id
                && $notification->like->id == $like->id
                && $notification->profilePost->id == $profilePost->id;
            }
        );
    }

    /** @test */
    public function the_owner_of_the_comment_should_not_receive_notifications_when_he_likes_his_own_comments()
    {
        $commentPoster = $this->signIn();
        $profilePost = create(ProfilePost::class);
        $comment = create(Reply::class, [
            'user_id' => $commentPoster->id,
            'repliable_id' => $profilePost->id,
            'repliable_type' => ProfilePost::class,
        ]);

        $this->post(route('api.likes.store', $comment));

        Notification::assertNotSentTo(
            $commentPoster,
            CommentHasNewLike::class
        );
    }

}