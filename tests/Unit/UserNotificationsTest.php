<?php

namespace Tests\Unit;

use App\Like;
use App\Notifications\CommentHasNewLike;
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

        $replyPoster = $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class));

        $liker = $this->signIn();

        $like = Like::create([
            'user_id' => $liker->id,
            'reply_id' => $reply->id,
        ]);

        $notification = new ReplyHasNewLike($liker, $like, $thread, $reply);

        $replyPoster->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($replyPoster));

    }

    /** @test */
    public function when_a_thread_has_new_reply_only_database_notifications_are_stored_when_emails_are_disabled()
    {
        Notification::fake();

        $threadPoster = $this->signIn();

        $thread = create(Thread::class);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,

        ]);

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

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
            'poster_id' => $poster->id,
        ]);

        $notification = new ProfileHasNewPost($post, $poster, $profileOwner);

        $profileOwner->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileOwner));

    }

    /** @test */
    public function the_participants_of_a_post_receive_email_and_database_notification_when_a_new_comment_is_added()
    {
        Notification::fake();

        $profileOwner = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
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

        $notification = new ProfilePostHasNewComment($post, $georgeComment, $george, $profileOwner);

        $george->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileOwner));

    }

    /** @test */
    public function the_owner_of_the_post_receives_email_and_database_notification_when_a_new_comment_is_added()
    {
        Notification::fake();

        $profileOwner = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
            'poster_id' => $poster->id,

        ]);

        $john = create(User::class);

        $johnComment = create(Reply::class, [
            'body' => 'john comment',
            'user_id' => $john->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $notification = new ProfilePostHasNewComment($post, $johnComment, $john, $profileOwner);

        $poster->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileOwner));
    }

    /** @test */
    public function the_owner_of_the_profile_receives_email_and_database_nottifications_when_a_new_comment_is_added_to_the_posts_on_his_profile()
    {
        Notification::fake();

        $profileOwner = create(User::class);

        $poster = $this->signIn();

        $post = create(ProfilePost::class, [
            'profile_owner_id' => $profileOwner->id,
            'poster_id' => $poster->id,

        ]);

        $john = create(User::class);

        $johnComment = create(Reply::class, [
            'body' => 'john comment',
            'user_id' => $john->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $post->id,
        ]);

        $notification = new ProfilePostHasNewComment($post, $johnComment, $john, $profileOwner);

        $profileOwner->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($profileOwner));
    }

    /** @test */
    public function the_comment_poster_receives_email_and_database_notifications_when_his_comment_is_liked()
    {
        Notification::fake();

        $profilePost = create(ProfilePost::class);
        $commentPoster = create(User::class);
        $liker = $this->signIn();

        $comment = create(Reply::class, [
            'user_id' => $commentPoster->id,
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $profilePost->id,
        ]);

        $like = Like::create([
            'user_id' => $liker->id,
            'reply_id' => $comment->id,
        ]);

        $notification = new CommentHasNewLike(
            $liker,
            $like,
            $comment,
            $commentPoster,
            $profilePost,
            $profilePost->profileOwner
        );

        $commentPoster->notify($notification);

        $this->assertEquals(['mail', 'database'], $notification->via($commentPoster));
    }

}