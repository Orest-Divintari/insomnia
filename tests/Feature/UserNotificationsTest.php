<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_subscribed_to_a_thread_receives_a_notification_when_a_new_reply_is_posted_by_another_user()
    {

        $thread = create(Thread::class);

        $user = $this->signIn();

        $thread->subscribe($user->id);

        $this->assertCount(0, $user->fresh()->notifications);

        $thread->addReply(raw(Reply::class));

        $this->assertCount(1, $user->fresh()->notifications);
    }

    /** @test */
    public function a_user_subscribed_to_a_thread_must_not_receive_notifications_about_his_own_replies()
    {
        $thread = create(Thread::class);

        $user = $this->signIn();

        $thread->subscribe($user->id);

        $this->assertCount(0, $user->fresh()->notifications);

        $thread->addReply(raw(Reply::class, [
            'user_id' => $user->id,
        ]));

        $this->assertCount(0, $user->fresh()->notifications);
    }

    /** @test */
    public function the_poster_of_a_reply_receives_notification_when_his_post_is_liked_by_another_user()
    {

        $user = create(User::class);

        $thread = create(Thread::class);

        $thread->subscribe($user->id);

        $reply = $thread->addReply(
            raw(Reply::class, ['user_id' => $user->id]
            ));

        $this->signIn();

        $this->assertCount(0, $user->fresh()->notifications);

        $this->post(route('api.likes.store', $reply));

        $this->assertCount(1, $user->fresh()->notifications);

    }

    /** @test */
    public function the_poster_of_the_reply_must_not_receive_a_notification_when_he_likes_his_own_reply()
    {

        $thread = create(Thread::class);

        $user = $this->signIn();

        $thread->subscribe($user->id);

        $reply = $thread->addReply(
            raw(Reply::class, ['user_id' => $user->id]
            ));

        $this->assertCount(0, $user->fresh()->notifications);

        $this->post(route('api.likes.store', $reply));

        $this->assertCount(0, $user->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_his_unread_notifications()
    {
        $thread = create(Thread::class);

        $user = $this->signIn();

        $thread->subscribe($user->id);

        $thread->addReply(raw(Reply::class));
        $thread->addReply(raw(Reply::class));

        $response = $this->get(route('api.user-notifications.index'))->json();

        $this->assertCount(2, $response);

    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        $thread = create(Thread::class);

        $user = $this->signIn();

        $thread->subscribe($user->id);

        $thread->addReply(raw(Reply::class));
        $thread->addReply(raw(Reply::class));

        $response = $this->get(route('api.user-notifications.index', $user))->json();

        $this->assertCount(2, $response);

        $firstNotification = $user->unreadNotifications->first();

        $this->delete(route('api.user-notifications.destroy', $firstNotification->id));

        $response = $this->get(route('api.user-notifications.index'))->json();

        $this->assertCount(1, $response);

    }

    /** @test */
    public function the_owner_of_a_profile_receives_notification_when_a_new_post_is_added_to_profile()
    {
        $this->signIn();
        $profileUser = create(User::class);
        $post = ['body' => 'some body'];
        $this->post(route('api.profile-posts.store', $profileUser), $post);

        $this->assertCount(1, $profileUser->notifications);
    }

    /** @test */
    public function participants_in_a_post_receive_notifications_when_a_new_comment_is_added_to_the_post()
    {
        $commentPoster = create(User::class, [
            'name' => 'azem',
        ]);
        $this->signIn($commentPoster);

        $profileUser = create(User::class);

        $profilePost = create(ProfilePost::class);

        $postParticipant = create(User::class, [
            'name' => 'john',
        ]);

        $participantsComment = create(Reply::class, [
            'body' => 'first comment',
            'repliable_type' => ProfilePost::class,
            'repliable_id' => $profilePost->id,
            'user_id' => $postParticipant->id,
        ]);

        $comment = ['body' => 'some comment'];

        $this->post(route('api.comments.store', $profilePost), $comment);

        $this->assertCount(1, $postParticipant->notifications);
    }

    /** @test */
    public function the_user_who_added_the_comment_should_not_receive_notifications_of_his_own_comments()
    {

        $commentPoster = $this->signIn();

        $profileUser = create(User::class);

        $profilePost = create(ProfilePost::class);

        $comment = ['body' => 'some comment'];

        $this->post(route('api.comments.store', $profilePost), $comment);

        $this->assertCount(0, $commentPoster->notifications);
    }

    /** @test */
    public function the_owner_of_the_profile_should_receive_notifications_of_new_comments_added_on_profile_posts_on_his_profile()
    {
        $this->signIn();

        $profileUser = create(User::class, [
            'name' => 'profile owner',
        ]);

        $profilePost = create(ProfilePost::class, [
            'profile_user_id' => $profileUser->id,
        ]);

        $comment = ['body' => 'some comment'];

        $this->post(route('api.comments.store', $profilePost), $comment);

        $this->assertCount(1, $profileUser->notifications);
    }

}