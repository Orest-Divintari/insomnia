<?php

namespace Tests\Feature\Threads;

use App\Category;
use App\Thread;
use App\User;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkThreadsAsReadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_an_authenticated_user_visits_a_thread_then_the_thread_is_marked_as_read()
    {
        $category = create(Category::class);
        $threads = ThreadFactory::inCategory($category)->createMany(2);
        $readThread = $threads->first();
        $user = $this->signIn();

        $this->get(route('threads.show', $readThread));

        $threads = $this->getJson(route('threads.index', $category))->json()['data'];
        $threads = collect($threads);
        $this->assertTrue(
            $threads->every(function ($thread, $key) use ($readThread) {
                if ($thread['id'] == $readThread->id) {
                    return $thread['has_been_updated'] == false;
                }
                return $thread['has_been_updated'] == true;
            })
        );
    }

    /** @test */
    public function when_a_guest_visits_a_thread_it_is_not_marked_as_read()
    {
        $category = create(Category::class);
        $threads = ThreadFactory::inCategory($category)->createMany(2);
        $visitedThread = $threads->first();

        $this->get(route('threads.show', $visitedThread));
        $threads = $this->getJson(route('threads.index', $category))->json()['data'];

        $threads = collect($threads);
        $this->assertTrue(
            $threads->every(function ($thread, $key) use ($visitedThread) {
                return $thread['has_been_updated'] == true;
            })
        );
    }

    /** @test */
    public function an_authenticated_user_can_mark_a_thread_as_read()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $this->assertTrue($thread->hasBeenUpdated());

        $this->patch(route('api.read-threads.update', $thread));

        $this->assertFalse($thread->hasBeenUpdated());
    }

    /** @test */
    public function guests_cannot_mark_a_thread_as_read()
    {
        $user = create(User::class);
        $thread = create(Thread::class);
        $this->assertTrue($thread->hasBeenUpdated());

        $this->patch(route('api.read-threads.update', $thread))
            ->assertRedirect('login');

        $this->assertTrue($thread->hasBeenUpdated());
    }
}