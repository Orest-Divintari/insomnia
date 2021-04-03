<?php

namespace Tests\Unit;

use App\Notifications\CommentHasNewLike;
use App\Notifications\ConversationHasNewMessage;
use App\Notifications\MessageHasNewLike;
use App\Notifications\ProfileHasNewPost;
use App\Notifications\ProfilePostHasNewComment;
use App\Notifications\ReplyHasNewLike;
use App\Notifications\ThreadHasNewReply;
use App\Notifications\YouHaveANewFollower;
use App\ProfilePost;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function when_a_thread_has_new_reply_then_email_and_database_notifications_are_sent_to_thread_subscribers()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);
        $reply = ReplyFactory::toThread($thread)->create();
        $notification = new ThreadHasNewReply($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }

    /** @test */
    public function when_a_reply_is_liked_a_database_notification_is_sent_to_reply_poster()
    {
        Notification::fake();
        $user = $this->signIn();
        $threadReply = ReplyFactory::by($user)->create();
        $liker = $this->signIn();
        $like = $threadReply->likedBy($liker);
        $notification = new ReplyHasNewLike($liker, $like, $threadReply->repliable, $threadReply);

        $user->notify($notification);

        $this->assertEquals(['database'], $notification->via($user));

    }

    /** @test */
    public function when_a_conversation_message_is_liked_then_a_database_notification_is_sent()
    {
        $conversationStarter = $this->signIn();
        $liker = create(User::class);
        $conversation = ConversationFactory::withParticipants([$liker->name])->create();
        $message = $conversation->messages->first();
        $this->signIn($liker);
        $like = $message->likedBy($liker);
        $notification = new MessageHasNewLike(
            $like,
            $liker,
            $conversation,
            $message
        );

        $conversationStarter->notify($notification);

        $this->assertEquals(
            ['database'],
            $notification->via($conversationStarter)
        );
        $this->assertNotEquals(
            ['email'],
            $notification->via($conversationStarter)
        );
    }

    /** @test */
    public function when_a_thread_has_new_reply_only_database_notifications_are_stored_when_emails_are_disabled()
    {
        Notification::fake();
        $threadPoster = $this->signIn();
        $thread = create(Thread::class);
        $reply = ReplyFactory::by($threadPoster)
            ->toThread($thread)
            ->create();
        $threadPoster->subscription($thread->id)->disableEmails();
        $notification = new ThreadHasNewReply($thread, $reply);

        $threadPoster->notify($notification);

        $this->assertEquals(['database'], $notification->via($threadPoster));
        $this->assertNotEquals(['mail', 'database'], $notification->via($threadPoster));
    }

    /** @test */
    public function profile_owner_receives_email_and_database_notification_when_a_new_post_is_added_to_profile()
    {
        Notification::fake();
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();
        $notification = new ProfileHasNewPost($profilePost, $poster, $profileOwner);

        $profileOwner->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileOwner));
    }

    /** @test */
    public function the_participants_of_a_profile_post_receive_email_and_database_notification_when_a_new_comment_is_added()
    {
        Notification::fake();
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();
        $john = create(User::class);
        CommentFactory::by($john)
            ->toProfilePost($profilePost)
            ->create();

        $george = create(User::class);
        $commentByGeorge = CommentFactory::by($george)
            ->toProfilePost($profilePost)
            ->create();
        $notification = new ProfilePostHasNewComment(
            $profilePost,
            $commentByGeorge,
            $george,
            $profileOwner
        );

        $john->notify($notification);

        $this->assertEquals(
            ['mail', 'database'],
            $notification->via($john)
        );
    }

    /** @test */
    public function the_owner_of_the_post_receives_email_and_database_notification_when_a_new_comment_is_added()
    {
        Notification::fake();
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();
        $john = create(User::class);
        $commentByJohn = CommentFactory::by($john)
            ->toProfilePost($profilePost)
            ->create();
        $notification = new ProfilePostHasNewComment(
            $profilePost,
            $commentByJohn,
            $john,
            $profileOwner
        );

        $poster->notify($notification);

        $this->assertEquals(
            ['mail', 'database'],
            $notification->via($poster)
        );
    }

    /** @test */
    public function the_owner_of_the_profile_receives_email_and_database_nottifications_when_a_new_comment_is_added_to_the_posts_on_his_profile()
    {
        Notification::fake();
        $profileOwner = create(User::class);
        $profileOwner = create(User::class);
        $poster = $this->signIn();
        $profilePost = ProfilePostFactory::by($poster)
            ->toProfile($profileOwner)
            ->create();
        $john = create(User::class);
        $commentByJohn = CommentFactory::by($john)
            ->toProfilePost($profilePost)
            ->create();
        $notification = new ProfilePostHasNewComment(
            $profilePost,
            $commentByJohn,
            $john,
            $profileOwner
        );
        $notification = new ProfilePostHasNewComment(
            $profilePost,
            $commentByJohn,
            $john,
            $profileOwner
        );

        $profileOwner->notify($notification);

        $this->assertEquals(
            ['mail', 'database'],
            $notification->via($profileOwner)
        );
    }

    /** @test */
    public function the_comment_poster_receives_email_and_database_notifications_when_his_comment_is_liked()
    {
        Notification::fake();
        $profilePost = create(ProfilePost::class);
        $commentPoster = create(User::class);
        $liker = $this->signIn();
        $comment = CommentFactory::by($commentPoster)
            ->toProfilePost($profilePost)
            ->create();
        $like = $comment->likedBy($liker);
        $notification = new CommentHasNewLike(
            $liker,
            $like,
            $comment,
            $commentPoster,
            $profilePost,
            $profilePost->profileOwner
        );

        $commentPoster->notify($notification);

        $this->assertEquals(
            ['mail', 'database'],
            $notification->via($commentPoster)
        );
    }

    /** @test */
    public function the_participants_receive_an_email_notification_when_a_new_message_is_added_to_conversation()
    {
        Notification::fake();
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->addMessage(['body' => $this->faker->sentence]);
        $notification = new ConversationHasNewMessage($conversation, $message);

        $participant->notify($notification);

        $this->assertEquals(['mail'], $notification->via($participant));
    }

    /** @test */
    public function when_user_starts_following_another_user_then_a_notification_is_sent_to_the_following_user()
    {
        Notification::fake();
        $follower = $this->signIn();
        $followingUser = create(User::class);
        $follower->follow($followingUser);
        $followDate = Carbon::now();
        $notification = new YouHaveANewFollower($follower, $followingUser, $followDate);

        $followingUser->notify($notification);

        $this->assertEquals(['database'], $notification->via($followingUser));
    }

}