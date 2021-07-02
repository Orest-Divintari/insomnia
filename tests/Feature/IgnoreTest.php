<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class IgnoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_be_ignored_by_another_user()
    {
        $john = $this->signIn();
        $doe = create(User::class);

        $this->post(route('ajax.user-ignorations.store', $doe));

        $this->assertTrue($doe->isIgnored($john));
    }

    /** @test */
    public function users_cannot_ignore_themselves()
    {
        $john = $this->signIn();

        $response = $this->post(route('ajax.user-ignorations.store', $john));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse($john->isIgnored($john));
    }

    /** @test */
    public function a_user_cannot_be_ignored_twice_by_the_same_user()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $doe->markAsIgnored($john);

        $response = $this->post(route('ajax.user-ignorations.store', $doe));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, $doe->ignorations);
    }

    /** @test */
    public function a_user_can_be_uningored_by_another_user()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $doe->markAsIgnored($john);

        $this->delete(route('ajax.user-ignorations.destroy', $doe));

        $this->assertFalse($doe->isIgnored($john));
    }

    /** @test */
    public function a_user_can_mark_a_thread_as_ignored()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $thread = ThreadFactory::by($doe)->create();

        $this->post(route('ajax.thread-ignorations.store', $thread));

        $this->assertTrue($thread->isIgnored($john));
    }

    /** @test */
    public function a_user_can_mark_a_thread_as_ignored_only_once()
    {
        $john = $this->signIn();
        $thread = create(Thread::class);
        $thread->markAsIgnored($john);

        $response = $this->post(route('ajax.thread-ignorations.store', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertCount(1, $thread->ignorations);
    }

    /** @test */
    public function users_cannot_ignore_their_own_threads()
    {
        $john = $this->signIn();
        $thread = ThreadFactory::by($john)->create();

        $response = $this->post(route('ajax.thread-ignorations.store', $thread));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse($thread->isIgnored($john));
    }

    /** @test */
    public function a_user_can_mark_a_thread_as_unignored()
    {
        $john = $this->signIn();
        $thread = create(Thread::class);
        $thread->markAsIgnored($john);

        $this->delete(route('ajax.thread-ignorations.destroy', $thread));

        $this->assertFalse($thread->isIgnored($john));
    }
}