<?php

namespace Tests\Feature;

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
    public function a_user_receives_a_notification_when_his_repy_is_quoted_by_another_user()
    {

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

}