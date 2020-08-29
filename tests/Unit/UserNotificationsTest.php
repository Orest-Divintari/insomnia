<?php

namespace Tests\Unit;

use App\Notifications\ProfileHasNewPost;
use App\Notifications\ProfilePostHasNewComment;
use App\Notifications\ReplyHasNewLike;
use App\Notifications\ThreadHasNewReply;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_thread_has_new_reply_a_notification_is_sent_to_email_and_database()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply(raw(Reply::class));

        $notification = new ThreadHasNewReply($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));

    }

    /** @test */
    public function when_a_reply_is_liked_a_notification_is_sent_to_email_and_database()
    {
        Notification::fake();

        $user = $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class));

        $notification = new ReplyHasNewLike($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($user));

    }

    /** @test */
    public function when_a_thread_has_new_reply_only_database_notifications_are_stored_when_emails_are_disabled()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply(raw(Reply::class));

        $user->subscription($thread->id)->disableEmails();

        $notification = new ThreadHasNewReply($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['database'], $notification->via($user));

        $this->assertNotEquals(['mail', 'database'], $notification->via($user));
    }

    /** @test */
    public function when_a_reply_has_new_like_only_database_notifications_are_stored_when_emails_are_disabled()
    {
        Notification::fake();
        $user = $this->signIn();
        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply(raw(Reply::class));

        $user->subscription($thread->id)->disableEmails();

        $notification = new ReplyHasNewLike($thread, $reply);

        $user->notify($notification);

        $this->assertEquals(['database'], $notification->via($user));

        $this->assertNotEquals(['mail', 'database'], $notification->via($user));
    }

    /** @test */
    public function profile_owner_receives_email_and_database_notification_when_a_new_post_is_added_to_profile()
    {
        Notification::fake();

        $profileUser = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,
        ]);

        $notification = new ProfileHasNewPost($poster, $post);

        $profileUser->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileUser));

    }

    /** @test */
    public function the_participants_of_a_post_receive_email_and_database_notification_when_a_new_comment_is_added()
    {
        Notification::fake();

        $profileUser = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,

        ]);

        $john = create(User::class);

        $johnComment = create(Reply::class, [
            'body' => 'john comment',
            'user_id' => $john->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $george = create(User::class);

        $georgeComment = create(Reply::class, [
            'body' => 'george comment',
            'user_id' => $george->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $notification = new ProfilePostHasNewComment($post, $georgeComment, $george, $profileUser);

        $george->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileUser));

    }

    /** @test */
    public function the_owner_of_the_post_receives_email_and_database_notification_when_a_new_comment_is_added()
    {
        Notification::fake();

        $profileUser = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,

        ]);

        $john = create(User::class);

        $johnComment = create(Reply::class, [
            'body' => 'john comment',
            'user_id' => $john->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $notification = new ProfilePostHasNewComment($post, $johnComment, $john, $profileUser);

        $poster->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileUser));
    }

    /** @test */
    public function the_owner_of_the_profile_receives_email_and_database_nottifications_when_a_new_comment_is_added_to_the_posts_on_his_profile()
    {
        Notification::fake();

        $profileUser = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
            'poster_id' => $poster->id,

        ]);

        $john = create(User::class);

        $johnComment = create(Reply::class, [
            'body' => 'john comment',
            'user_id' => $john->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $notification = new ProfilePostHasNewComment($post, $johnComment, $john, $profileUser);

        $profileUser->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileUser));
    }

}