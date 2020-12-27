<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateThreadRepliesTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid message.';

    /** @test */
    public function unauthorized_users_cannot_update_a_reply()
    {
        $reply = ReplyFactory::create();
        $this->signIn();
        $newBody = ['body' => 'changed body'];

        $response = $this->patch(
            route('api.replies.update', $reply),
            $newBody
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authorized_users_may_update_a_reply()
    {
        $replyPoster = $this->signIn();
        $reply = ReplyFactory::by($replyPoster)->create();
        $newBody = ['body' => 'changed body'];

        $this->patch(
            route('api.replies.update', $reply),
            $newBody
        );

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
    public function when_updating_a_thread_reply_a_body_is_required()
    {
        $replyPoster = $this->signIn();
        $reply = ReplyFactory::by($replyPoster)->create();
        $emptyReply = ['body' => ''];

        $response = $this->patchJson(
            route('api.replies.update', $reply),
            $emptyReply
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_thread_reply_a_string_must_be_given()
    {
        $replyPoster = $this->signIn();
        $reply = ReplyFactory::by($replyPoster)->create();
        $incorrectReply = ['body' => 15];

        $response = $this->patchJson(
            route('api.replies.update', $reply),
            $incorrectReply
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

}