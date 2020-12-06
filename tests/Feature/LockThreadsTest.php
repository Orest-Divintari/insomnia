<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->post(route('api.lock-threads.store', $thread));

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function guests_cannot_lock_a_thread()
    {
        $thread = create(Thread::class);

        $this->assertFalse($thread->locked);

        $this->post(route('api.lock-threads.store', $thread))
            ->assertRedirect('login');

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function an_authorized_user_can_unlock_a_thread()
    {

        $this->withoutExceptionHandling();
        $admin = $this->signInAdmin();
        $thread = create(Thread::class);

        $this->assertFalse($thread->locked);

        $this->post(route('api.lock-threads.store', $thread));

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

        $this->delete(route('api.lock-threads.destroy', $thread));

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function guests_cannot_unlock_a_thread()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->locked);
        $thread->lock();
        $this->assertTrue($thread->locked);

        $this->delete(route('api.lock-threads.destroy', $thread))
            ->assertRedirect('login');

        $this->assertTrue($thread->fresh()->locked);
    }
}