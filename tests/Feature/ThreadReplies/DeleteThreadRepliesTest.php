<?php

namespace Tests\Feature\ThreadReplies;

use App\Thread;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteThreadRepliesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_users_may_delete_a_reply()
    {
        $replyPoster = $this->signIn();
        $reply = ReplyFactory::by($replyPoster)->create();

        $this->delete(route('ajax.replies.destroy', $reply));

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

        $response = $this->delete(route('ajax.replies.destroy', $reply));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
        ]);
    }
}
