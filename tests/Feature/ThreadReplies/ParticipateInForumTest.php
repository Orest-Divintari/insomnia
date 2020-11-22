<?php

namespace Tests\Feature\ThreadReplies;

use App\Reply;
use App\Thread;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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

        $this->post(route('api.replies.store', $thread), $reply)
            ->assertRedirect('login');
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
    public function a_reply_body_must_be_of_stype_string()
    {
        $thread = create(Thread::class);
        $replyPoster = $this->signIn();
        $emptyReply = ['body' => ''];

        $response = $this->postJson(route('api.replies.store', $thread), $emptyReply)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);

    }
    /** @test */
    public function when_posting_a_thread_reply_a_body_is_required()
    {
        $thread = create(Thread::class);
        $replyPoster = $this->signIn();
        $emptyReply = ['body' => ''];

        $this->postJson(route('api.replies.store', $thread), $emptyReply)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_posting_a_thread_reply_the_body_must_be_of_type_string()
    {
        $thread = create(Thread::class);
        $replyPoster = $this->signIn();
        $incorrectReply = ['body' => 15];

        $this->postJson(route('api.replies.store', $thread), $incorrectReply)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_thread_reply_a_body_is_required()
    {
        $replyPoster = $this->signIn();

        $reply = ReplyFactory::by($replyPoster)->create();

        $emptyReply = ['body' => ''];

        $this->patchJson(route('api.replies.update', $reply), $emptyReply)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_thread_reply_a_string_must_be_given()
    {
        $replyPoster = $this->signIn();

        $reply = ReplyFactory::by($replyPoster)->create();

        $incorrectReply = ['body' => 15];

        $this->patchJson(route('api.replies.update', $reply), $incorrectReply)
            ->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function unauthorized_users_cannot_update_a_reply()
    {
        $reply = ReplyFactory::create();

        $this->signIn();

        $newBody = ['body' => 'changed body'];

        $this->patch(route('api.replies.update', $reply), $newBody)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authorized_users_may_update_a_reply()
    {
        $replyPoster = $this->signIn();

        $reply = ReplyFactory::by($replyPoster)->create();

        $newBody = ['body' => 'changed body'];

        $this->patch(route('api.replies.update', $reply), $newBody);

        $this->assertDatabaseMissing('replies', [
            'body' => 'old body',
            'user_id' => $replyPoster->id,
        ]);

        $this->assertDataBaseHas('replies', [
            'body' => $newBody['body'],
            'user_id' => $replyPoster->id,
        ]);

    }

    /** @test */
    public function authorized_users_may_delete_a_reply()
    {
        $replyPoster = $this->signIn();

        $reply = ReplyFactory::by($replyPoster)->create();

        $this->delete(route('api.replies.destroy', $reply));

        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id,
            'repliable_type' => Thread::class,
        ]);

    }

    /** @test */
    public function unauthorized_users_cannot_delete_a_reply()
    {
        $reply = ReplyFactory::create();

        $unauthorizedUser = $this->signIn();

        $this->delete(route('api.replies.destroy', $reply))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
        ]);
    }
}