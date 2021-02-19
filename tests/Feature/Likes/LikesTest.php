<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_like_a_reply()
    {
        $reply = ReplyFactory::create();

        $this->post(route('ajax.likes.store', $reply))
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_like_a_reply()
    {
        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $this->post(route('ajax.likes.store', $reply));

        $this->assertDatabaseHas('likes', [
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $reply->fresh()->likes);

    }

    /** @test */
    public function an_authenticateid_user_can_like_a_reply_only_once()
    {
        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $this->post(route('ajax.likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);

        $this->post(route('ajax.likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_unlike_a_reply()
    {

        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $this->post(route('ajax.likes.store', $reply));

        $this->assertDatabaseHas('likes', [
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $reply->fresh()->likes);

        $this->delete(route('ajax.likes.destroy', $reply));

        $this->assertDatabaseMissing('likes', [
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);
        $this->assertCount(0, $reply->fresh()->likes);

    }
}
