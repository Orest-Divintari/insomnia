<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_cannot_subscribe_to_a_thread()
    {
        $thread = create(Thread::class);
        $this->post(route('api.thread-subscriptions.store', $thread))
            ->assertRedirect('login');
    }

    /** @test */
    public function authenticated_users_can_subscribe_to_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $this->assertCount(0, $user->subscriptions);
        $this->assertCount(0, $thread->subscribers);

        $this->post(route('api.thread-subscriptions.store', $thread));

        $this->assertCount(1, $user->fresh()->subscriptions);
        $this->assertCount(1, $thread->fresh()->subscribers);
    }

    /** @test */
    public function authenticated_users_can_unsubscribe_to_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $this->post(route('api.thread-subscriptions.store', $thread));

        $this->assertCount(1, $user->subscriptions);
        $this->assertCount(1, $thread->subscribers);

        $this->delete(route('api.thread-subscriptions.destroy', $thread));

        $this->assertCount(0, $user->fresh()->subscriptions);
        $this->assertCount(0, $thread->fresh()->subscribers);
    }
}