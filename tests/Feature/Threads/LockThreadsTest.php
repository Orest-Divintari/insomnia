<?php

namespace Tests\Feature\Threads;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authorized_user_can_lock_a_thread()
    {
        $admin = $this->signInAdmin();
        $thread = create(Thread::class);
        $this->assertFalse($thread->locked);

        $this->post(route('api.lock-threads.store', $thread));

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function unathorized_users_cannot_lock_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $this->assertFalse($thread->locked);

        $response = $this->post(route('api.lock-threads.store', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function guests_cannot_lock_a_thread()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->locked);

        $response = $this->post(route('api.lock-threads.store', $thread));

        $response->assertRedirect('login');
        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function an_authorized_user_can_unlock_a_thread()
    {
        $admin = $this->signInAdmin();
        $thread = create(Thread::class);
        $thread->lock();
        $this->assertTrue($thread->fresh()->locked);

        $this->delete(route('api.lock-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function unathorized_users_cannot_unlock_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $this->assertFalse($thread->locked);
        $thread->lock();
        $this->assertTrue($thread->fresh()->locked);

        $response = $this->delete(route('api.lock-threads.destroy', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function guests_cannot_unlock_a_thread()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->locked);
        $thread->lock();
        $this->assertTrue($thread->locked);

        $response = $this->delete(
            route('api.lock-threads.destroy', $thread)
        );

        $response->assertRedirect('login');
        $this->assertTrue($thread->fresh()->locked);
    }
}