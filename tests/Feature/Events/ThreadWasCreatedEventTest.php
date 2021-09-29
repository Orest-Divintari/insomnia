<?php

namespace Tests\Feature\Events;

use App\Listeners\Thread\NotifyMentionedUsersInThread;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class ThreadWasCreatedEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_notifies_the_mentioned_users_in_the_thread_body()
    {
        $this->withoutExceptionHandling();
        $listener = Mockery::spy(NotifyMentionedUsersInThread::class);
        app()->instance(NotifyMentionedUsersInThread::class, $listener);
        $user = $this->signIn();
        $category = create(Category::class);
        $attributes = [
            'body' => $this->faker()->sentence(),
            'title' => $this->faker()->sentence(),
            'category_id' => $category->id,

        ];

        $this->post(route('threads.store'), $attributes);

        $thread = $user->threads()->first();

        $listener->shouldHaveReceived('handle', function ($event) use ($thread) {
            return $event->thread->is($thread)
            && $event->threadPoster->is($thread->poster);
        });
    }
}