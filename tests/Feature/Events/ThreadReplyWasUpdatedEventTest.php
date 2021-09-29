<?php

namespace Tests\Feature\Events;

use App\Listeners\Thread\NotifyMentionedUsersInThreadReply;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class ThreadReplyWasUpdatedEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_notifies_the_mentioned_users_in_a_thread_reply()
    {
        $this->withoutExceptionHandling();
        $listener = Mockery::spy(NotifyMentionedUsersInThreadReply::class);
        app()->instance(NotifyMentionedUsersInThreadReply::class, $listener);
        $user = $this->signIn();
        $thread = create(Thread::class);
        $reply = $thread->replies()->first();
        $attributes = [
            'body' => $this->faker()->sentence(),
        ];

        $this->patch(route('ajax.replies.update', $reply), $attributes);

        $listener->shouldHaveReceived('handle', function ($event) use ($thread, $reply) {
            return $event->thread->is($thread)
            && $event->reply->is($reply);
        });
    }
}