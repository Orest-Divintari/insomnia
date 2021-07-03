<?php

namespace Tests\Feature\ThreadSubcriptions;

use App\Category;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_may_automatically_subscribe_to_the_content_they_create()
    {
        $user = $this->signIn();
        $category = create(Category::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'body' => $this->faker()->text(),
            'category_id' => $category->id,
        ];

        $this->post(route('threads.store'), $attributes);

        $thread = $category->threads()->first();

        $this->assertTrue($thread->hasSubscriber($user));
    }

    /** @test */
    public function users_may_disable_the_option_to_automatically_subscribe_to_the_content_they_create()
    {
        $user = $this->signIn();
        $user->preferences()->merge(['subscribe_on_creation' => false]);
        $category = create(Category::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'body' => $this->faker()->text(),
            'category_id' => $category->id,
        ];

        $this->post(route('threads.store'), $attributes);

        $thread = $category->threads()->first();

        $this->assertFalse($thread->hasSubscriber($user));
    }

    /** @test */
    public function users_may_automatically_receive_email_notifications_from_the_subscriptions_for_the_content_they_create()
    {
        $user = $this->signIn();
        $category = create(Category::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'body' => $this->faker()->text(),
            'category_id' => $category->id,
        ];

        $this->post(route('threads.store'), $attributes);

        $thread = $category->threads()->first();

        $this->assertTrue($user->subscription($thread->id)->prefers_email);
    }

    /** @test */
    public function users_may_disable_automatic_email_notifications_from_the_subscriptions_for_the_content_they_create()
    {
        $user = $this->signIn();
        $user->preferences()->merge(['subscribe_on_creation_with_email' => false]);

        $category = create(Category::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'body' => $this->faker()->text(),
            'category_id' => $category->id,
        ];

        $this->post(route('threads.store'), $attributes);

        $thread = $category->threads()->first();
        $this->assertFalse($user->subscription($thread->id)->prefers_email);
    }

    /** @test */
    public function the_automatic_email_notifications_from_a_subscription_on_content_creation_cannot_be_enabled_unless_user_subscribes_to_the_content()
    {
        $user = $this->signIn();
        $user->preferences()->merge(['subscribe_on_creation' => false]);
        $user->preferences()->merge(['subscribe_on_creation_with_email' => true]);

        $category = create(Category::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'body' => $this->faker()->text(),
            'category_id' => $category->id,
        ];

        $this->post(route('threads.store'), $attributes);

        $thread = $category->threads()->first();
        $this->assertFalse($thread->hasSubscriber($user));
    }

    /** @test */
    public function users_may_automatically_subscribe_to_the_content_they_interact_with()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $attributes = [
            'body' => $this->faker()->text(),
        ];

        $this->postJson(route('ajax.replies.store', $thread), $attributes);

        $this->assertTrue($thread->hasSubscriber($user));
    }

    /** @test */
    public function users_may_disable_automatic_subscription_to_the_content_they_interact_with()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $user->preferences()->merge(['subscribe_on_interaction' => false]);
        $attributes = [
            'body' => $this->faker()->text(),
        ];

        $this->postJson(route('ajax.replies.store', $thread), $attributes);

        $this->assertFalse($thread->hasSubscriber($user));
    }

    /** @test */
    public function users_may_automatically_receive_email_notifications_from_the_subscription_for_the_content_they_interact_with()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $attributes = [
            'body' => $this->faker()->text(),
        ];

        $this->postJson(route('ajax.replies.store', $thread), $attributes);

        $this->assertTrue($user->subscription($thread->id)->prefers_email);
    }

    /** @test */
    public function users_may_disable_automatic_email_notifications_from_the_subscription_for_the_content_they_interact_with()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $user->preferences()->merge(['subscribe_on_interaction_with_email' => false]);
        $attributes = [
            'body' => $this->faker()->text(),
        ];

        $this->postJson(route('ajax.replies.store', $thread), $attributes);

        $this->assertFalse($user->subscription($thread->id)->prefers_email);
    }

    /** @test */
    public function the_automatic_email_notifications_from_a_subscription_on_content_interaction_cannot_be_enabled_unless_user_subscribes_to_the_content()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $user->preferences()->merge(['subscribe_on_interaction' => false]);
        $user->preferences()->merge(['subscribe_on_interaction_with_email' => true]);
        $attributes = [
            'body' => $this->faker()->text(),
        ];

        $this->postJson(route('ajax.replies.store', $thread), $attributes);

        $this->assertFalse($thread->hasSubscriber($user));
    }

    /** @test */
    public function guests_cannot_subscribe_to_a_thread()
    {
        $thread = create(Thread::class);
        $this->put(route('ajax.thread-subscriptions.update', $thread))
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
    public function authenticated_users_can_subscribe_to_a_thread_and_enable_email_notifications()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->assertCount(0, $user->subscriptions);
        $this->assertCount(0, $thread->subscriptions);

        $prefersEmail = [
            'email_notifications' => true,
        ];

        $this->put(
            route('ajax.thread-subscriptions.update', $thread),
            $prefersEmail
        );

        tap($user->fresh(), function ($user) {
            $this->assertCount(1, $user->subscriptions);
            $this->assertTrue($user->subscriptions->first()->prefers_email);
        });
    }

    /** @test */
    public function authenticated_users_can_subscribe_to_a_thread_only_once()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $thread->subscribe($user->id);

        $response = $this->put(route('ajax.thread-subscriptions.update', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authenticated_users_can_subscribe_to_a_thread_without_email_notifications()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->assertCount(0, $user->subscriptions);
        $this->assertCount(0, $thread->subscriptions);
        $prefersEmail = [
            'email_notifications' => false,
        ];

        $this->put(
            route('ajax.thread-subscriptions.update', $thread),
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
            route('ajax.thread-subscriptions.update', $thread),
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
        $this->put(route('ajax.thread-subscriptions.update', $thread), $prefersEmail);

        $this->assertCount(1, $user->subscriptions);
        $this->assertCount(1, $thread->subscriptions);

        $this->delete(route('ajax.thread-subscriptions.destroy', $thread));

        $this->assertCount(0, $user->fresh()->subscriptions);
        $this->assertCount(0, $thread->fresh()->subscriptions);
    }

}