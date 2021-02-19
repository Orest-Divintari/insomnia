<?php

namespace Tests\Feature\ThreadReplies;

use App\Exceptions\PostThrottlingException;
use App\Http\Middleware\ThrottlePosts;
use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThreadRepliesTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);
        $this->errorMessage = 'Please enter a valid message.';
    }

    /** @test */
    public function guests_may_not_post_replies_to_a_thread()
    {
        $thread = create(Thread::class);
        $reply = raw(Reply::class);

        $response = $this->post(
            route('ajax.replies.store', $thread),
            $reply
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function authorized_users_may_post_replies_to_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $this->post(route('ajax.replies.store', $thread), ['body' => 'some body']);

        $this->assertDatabaseHas('replies', [
            'body' => 'some body',
            'user_id' => $user->id,
        ]);
        // first reply == body of thread
        $this->assertCount(2, $thread->replies);

    }

    /** @test */
    public function when_posting_a_thread_reply_a_body_is_required()
    {
        $thread = create(Thread::class);
        $replyPoster = $this->signIn();
        $emptyReply = ['body' => ''];

        $response = $this->postJson(route('ajax.replies.store', $thread), $emptyReply);

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_posting_a_thread_reply_the_body_must_be_of_type_string()
    {
        $thread = create(Thread::class);
        $replyPoster = $this->signIn();
        $incorrectReply = ['body' => 15];

        $response = $this->postJson(
            route('ajax.replies.store', $thread),
            $incorrectReply
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_user_cannot_add_a_reply_if_has_exceeded_the_post_rate_limit()
    {
        $this->withMiddleware([ThrottlePosts::class]);
        $this->withoutExceptionHandling();
        $this->signIn();
        $errorMessage = 'You must wait';
        $this->expectException(PostThrottlingException::class);

        $this->post(
            route('threads.store'),
            raw(Thread::class)
        );
        $thread = Thread::first();
        $response = $this->post(
            route('ajax.replies.store', $thread),
            raw(Reply::class)
        );

        $this->assertTrue(str_contains(
            $response->getContent(),
            $errorMessage
        ));
    }
}
