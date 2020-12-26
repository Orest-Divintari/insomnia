<?php

namespace Tests\Feature\ThreadReplies;

use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage;

    public function setUp(): void
    {
        parent::setUp();
        $this->errorMessage = 'Please enter a valid message.';
    }

    /** @test */
    public function guests_may_not_post_replies_to_a_thread()
    {
        $thread = create(Thread::class);
        $reply = raw(Reply::class);

        $response = $this->post(
            route('api.replies.store', $thread),
            $reply
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function authorized_users_may_post_replies_to_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $this->post(route('api.replies.store', $thread), ['body' => 'some body']);

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

        $response = $this->postJson(route('api.replies.store', $thread), $emptyReply);

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
            route('api.replies.store', $thread),
            $incorrectReply
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }
}