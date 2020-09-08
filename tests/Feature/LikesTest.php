<?php

namespace Tests\Feature;

use App\Like;
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

        $this->post(route('api.likes.store', $reply))
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_like_a_reply()
    {
        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $this->post(route('api.likes.store', $reply));

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

        $this->post(route('api.likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);

        $this->post(route('api.likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_unlike_a_reply()
    {

        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $this->post(route('api.likes.store', $reply));

        $this->assertDatabaseHas('likes', [
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $reply->fresh()->likes);

        $this->delete(route('api.likes.destroy', $reply));

        $this->assertDatabaseMissing('likes', [
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);
        $this->assertCount(0, $reply->fresh()->likes);

    }

    /** @test */
    public function when_a_reply_is_deleted_all_the_associated_likes_are_deleted()
    {
        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $reply->likedBy($user);

        $this->assertCount(1, $reply->likes);

        $reply->delete();

        $this->assertEquals(0, Like::all()->count());
    }

}