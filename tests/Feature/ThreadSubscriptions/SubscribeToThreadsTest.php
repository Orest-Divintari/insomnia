<?php

namespace Tests\Feature\ThreadSubcriptions;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_subscribe_to_a_thread()
    {
        $thread = create(Thread::class);
        $this->put(route('api.thread-subscriptions.update', $thread))
            ->assertRedirect('login');
    }

    /** @test */
    public function when_a_user_creates_a_thread_automatically_is_subscribed_to_newly_created_thread()
    {
        $user = $this->signIn();

        $thread = raw(Thread::class);

        $title = ['title' => $thread['title']];

        $response = $this->post(route('threads.store'), $thread);

        $this->assertCount(1, $user->fresh()->subscriptions);
    }

    /** @test */
    public function authenticated_users_can_subscribe_to_existing_thread_and_enable_email_notifications()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->assertCount(0, $user->subscriptions);
        $this->assertCount(0, $thread->subscriptions);
        $prefersEmail = [
            'email_notifications' => true,
        ];

        $this->put(
            route('api.thread-subscriptions.update', $thread),
            $prefersEmail)
        ;

        tap($user->fresh(), function ($user) {
            $this->assertCount(1, $user->subscriptions);
            $this->assertTrue($user->subscriptions->first()->prefers_email);
        });
    }

    /** @test */
    public function authenticated_users_can_subscribe_to_existing_thread_and_disable_email_notifications()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->assertCount(0, $user->subscriptions);
        $this->assertCount(0, $thread->subscriptions);
        $prefersEmail = [
            'email_notifications' => false,
        ];

        $this->put(
            route('api.thread-subscriptions.update', $thread),
            $prefersEmail
        );

        tap($user->fresh(), function ($user) {
            $this->assertCount(1, $user->subscriptions);
            $this->assertFalse($user->subscriptions->first()->prefersEmails());
        });
    }

    /** @test */
    public function thread_subscription_requiress_email_notification_preference()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->assertCount(0, $user->subscriptions);
        $this->assertCount(0, $thread->subscriptions);

        $response = $this->put(
            route('api.thread-subscriptions.update', $thread),
            []
        );

        $response->assertSessionHasErrors('email_notifications');
    }

    /** @test */
    public function authenticated_users_can_unsubscribe_from_a_thread()
    {
        Notification::fake();

        $thread = create(Thread::class);

        $user = $this->signIn();

        $prefersEmail = [
            'email_notifications' => true,
        ];
        $this->put(route('api.thread-subscriptions.update', $thread), $prefersEmail);

        $this->assertCount(1, $user->subscriptions);
        $this->assertCount(1, $thread->subscriptions);

        $this->delete(route('api.thread-subscriptions.destroy', $thread));

        $this->assertCount(0, $user->fresh()->subscriptions);
        $this->assertCount(0, $thread->fresh()->subscriptions);
    }

}