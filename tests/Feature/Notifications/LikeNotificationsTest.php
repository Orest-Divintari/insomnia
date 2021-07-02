<?php

namespace Tests\Feature\Notifications;

use App\Notifications\CommentHasNewLike;
use App\Notifications\MessageHasNewLike;
use App\Notifications\ProfilePostHasNewLike;
use App\Notifications\ReplyHasNewLike;
use App\ProfilePost;
use App\Reply;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
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
    public function users_will_not_receive_a_notification_when_their_thread_replies_are_liked_by_ignored_users()
    {
        $this->withoutExceptionHandling();
        Notification::fake();
        $john = $this->signIn();
        $reply = ReplyFactory::by($john)->create();
        $doe = create(User::class);
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.reply-likes.store', $reply));

        Notification::assertNotSentTo($john, ReplyHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_their_profile_post_comments_are_liked_by_ignored_users()
    {
        Notification::fake();
        $john = $this->signIn();
        $comment = CommentFactory::by($john)->create();
        $doe = create(User::class);
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.reply-likes.store', $comment));

        Notification::assertNotSentTo($john, CommentHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_their_conversation_messages_are_liked_by_ignored_users()
    {
        Notification::fake();
        $john = $this->signIn();
        $doe = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$doe->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.reply-likes.store', $message));

        Notification::assertNotSentTo($john, MessageHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_their_profile_post_is_liked_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $bob = create(User::class);
        $profilePost = ProfilePostFactory::by($john)->toProfile($bob)->create();
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        Notification::assertNotSentTo($john, ProfilePostHasNewLike::class);
    }

    /** @test */
    public function users_may_receive_database_notifications_when_another_user_likes_their_thread_replies()
    {
        $user = create(User::class);
        $reply = ReplyFactory::by($user)->create();
        $liker = $this->signIn();
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.reply-likes.store', $reply));

        Notification::assertSentTo($user, function (ReplyHasNewLike $notification, $channels) use ($reply, $desiredChannels) {
            return $reply->id == $notification->reply->id &&
                $desiredChannels == $channels;
        });
    }

    /** @test */
    public function users_may_disable_database_notifications_when_another_user_likes_their_thread_replies()
    {
        $user = create(User::class);
        $user->preferences()->merge(['thread_reply_liked' => []]);
        $reply = ReplyFactory::by($user)->create();
        $liker = $this->signIn();
        $desiredChannels = [];

        $this->postJson(route('ajax.reply-likes.store', $reply));

        Notification::assertSentTo($user, function (ReplyHasNewLike $notification, $channels) use ($reply, $desiredChannels) {
            return $reply->id == $notification->reply->id &&
                $desiredChannels == $channels;
        });
    }
    /** @test */
    public function the_thread_reply_poster_must_not_receive_a_notification_when_they_like_their_own_reply()
    {
        $replyPoster = $this->signIn();
        $reply = ReplyFactory::by($replyPoster)->create();

        $this->post(route('ajax.reply-likes.store', $reply));

        Notification::assertNotSentTo($replyPoster, ReplyHasNewLike::class);
    }

    /** @test */
    public function users_may_receive_a_database_notification_when_other_participants_like_their_messages()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($participant);

        $this->postJson(route('ajax.reply-likes.store', $message));

        Notification::assertSentTo($conversationStarter, function (MessageHasNewLike $notification, $channels) use ($message) {
            return $message->id == $notification->message->id &&
            empty(array_diff_assoc($channels, ['database']));
        });
    }

    /** @test */
    public function users_may_disable_database_notifications_when_their_messages_are_liked_by_other_participants()
    {
        $conversationStarter = $this->signIn();
        $conversationStarter->preferences()->merge(['message_liked' => []]);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($participant);

        $this->postJson(route('ajax.reply-likes.store', $message));

        Notification::assertSentTo($conversationStarter, function (MessageHasNewLike $notification, $channels) use ($message) {
            return $message->id == $notification->message->id &&
            empty($channels);
        });
    }

    /** @test */
    public function the_conversation_message_poster_should_not_receive_a_notification_when_they_like_their_own_message()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();
        $message = $conversation->messages->first();

        $this->post(route('ajax.reply-likes.store', $message));

        Notification::assertNotSentTo(
            $conversationStarter,
            MessageHasNewLike::class,
        );
    }

    /** @test */
    public function comment_posters_may_receive_database_notifications_when_another_user_likes_their_comment()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $commentPoster = create(User::class);
        $comment = CommentFactory::by($commentPoster)
            ->toProfilePost($profilePost)
            ->create();
        $liker = $this->signIn();
        $desiredChannels = ['database'];

        $this->post(route('ajax.reply-likes.store', $comment));

        $like = $comment->likes()->first();
        Notification::assertSentTo(
            $commentPoster,
            function (CommentHasNewLike $notification, $channels)
             use ($liker, $like, $comment, $commentPoster, $profilePost, $profileOwner, $desiredChannels) {

                return $notification->like->is($like) &&
                $notification->liker->is($liker) &&
                $notification->comment->is($comment) &&
                $notification->commentPoster->is($commentPoster) &&
                $notification->profilePost->is($profilePost) &&
                $notification->profileOwner->is($profileOwner) &&
                    $channels == $desiredChannels;
            });
    }

    /** @test */
    public function comment_posters_may_disable_database_notifications_when_another_user_likes_their_comment()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $commentPoster = create(User::class);
        $commentPoster->preferences()->merge(['comment_liked' => []]);
        $comment = CommentFactory::by($commentPoster)
            ->toProfilePost($profilePost)
            ->create();
        $liker = $this->signIn();
        $desiredChannels = [];

        $this->post(route('ajax.reply-likes.store', $comment));

        $like = $comment->likes()->first();
        Notification::assertSentTo(
            $commentPoster,
            function (CommentHasNewLike $notification, $channels)
             use ($liker, $like, $comment, $commentPoster, $profilePost, $profileOwner, $desiredChannels) {

                return $notification->like->is($like) &&
                $notification->liker->is($liker) &&
                $notification->comment->is($comment) &&
                $notification->commentPoster->is($commentPoster) &&
                $notification->profilePost->is($profilePost) &&
                $notification->profileOwner->is($profileOwner) &&
                    $channels == $desiredChannels;
            });
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

        $this->post(route('ajax.reply-likes.store', $comment));

        Notification::assertNotSentTo(
            $commentPoster,
            CommentHasNewLike::class
        );
    }

    /** @test */
    public function users_receive_database_notifications_when_their_profile_post_is_liked_by_another_user()
    {
        $poster = create(User::class);
        $profilePost = ProfilePostFactory::by($poster)->create();
        $profileOwner = $profilePost->profileOwner;
        $liker = $this->signIn();
        $desiredChannels = ['database'];

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        $like = $profilePost->likes()->first();
        Notification::assertSentTo(
            $poster,
            function (ProfilePostHasNewLike $notification, $channels) use ($desiredChannels, $profileOwner, $poster, $profilePost, $liker, $like) {
                return $notification->poster->is($poster) &&
                $notification->profileOwner->is($profileOwner) &&
                $notification->profilePost->is($profilePost) &&
                $notification->liker->is($liker) &&
                $notification->like->is($like) &&
                    $desiredChannels == $channels;
            });

    }

    /** @test */
    public function users_may_disable_database_notifications_when_their_profile_post_is_liked_by_another_user()
    {
        $poster = create(User::class);
        $poster->preferences()->merge(['profile_post_liked' => []]);
        $profilePost = ProfilePostFactory::by($poster)->create();
        $profileOwner = $profilePost->profileOwner;
        $liker = $this->signIn();
        $desiredChannels = [];

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        $like = $profilePost->likes()->first();
        Notification::assertSentTo(
            $poster,
            function (ProfilePostHasNewLike $notification, $channels) use ($desiredChannels, $profileOwner, $poster, $profilePost, $liker, $like) {
                return $notification->poster->is($poster) &&
                $notification->profileOwner->is($profileOwner) &&
                $notification->profilePost->is($profilePost) &&
                $notification->liker->is($liker) &&
                $notification->like->is($like) &&
                    $desiredChannels == $channels;
            });

    }

    /** @test */
    public function users_who_create_a_profile_post_should_not_receive_notification_when_they_like_their_own_posts()
    {
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)->create();
        $profileOwner = $profilePost->profileOwner;
        $desiredChannels = ['database'];

        $this->postJson(route('ajax.profile-post-likes.store', $profilePost));

        Notification::assertNotSentTo($poster, ProfilePostHasNewLike::class);
    }
}