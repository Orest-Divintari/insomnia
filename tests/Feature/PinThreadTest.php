<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class PinThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admins_can_mark_a_thread_as_pinned()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->pinned);
        $admin = $this->signInAdmin();

        $this->patch(route('ajax.pin-threads.update', $thread));

        $this->assertTrue($thread->fresh()->pinned);
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_thread_as_pinned()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->pinned);
        $unathorizedUser = $this->signIn();

        $response = $this->patch(route('ajax.pin-threads.update', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse($thread->fresh()->pinned);
    }

    /** @test */
    public function admins_can_mark_a_thread_as_unpinned()
    {
        $thread = create(Thread::class);
        $thread->pin();
        $this->assertTrue($thread->pinned);
        $admin = $this->signInAdmin();

        $this->delete(route('ajax.pin-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->pinned);
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_thread_as_unpinned()
    {
        $thread = create(Thread::class);
        $thread->pin();
        $this->assertTrue($thread->pinned);
        $unathorizedUser = $this->signIn();

        $response = $this->delete(route('ajax.pin-threads.destroy', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertTrue($thread->fresh()->pinned);
    }
}
