<?php

namespace Tests\Feature\ThreadReplies;

use App\Http\Middleware\ThrottlePosts;
use App\Listeners\Subscription\NotifyThreadSubscribers;
use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NewReplyWasPostedToThreadEventTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function when_a_user_posts_a_reply_to_a_thread_then_an_event_is_fired()
    {
        $poster = $this->signIn();
        $thread = create(Thread::class);
        $listener = Mockery::spy(NotifyThreadSubscribers::class);
        app()->instance(NotifyThreadSubscribers::class, $listener);
        $reply = ['body' => 'some reply'];

        $this->post(
            route('ajax.replies.store', $thread),
            ['body' => $reply['body']]
        );

        $reply = Reply::whereBody($reply['body'])->first();
        $listener->shouldHaveReceived('handle', function ($event) use ($thread, $reply) {
            return $event->thread->id == $thread->id
            && $event->reply->id == $reply->id;
        });
    }
}
