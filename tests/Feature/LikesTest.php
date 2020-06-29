<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_like_a_reply()
    {
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->post(route('api.likes.store', $reply))
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_like_a_reply()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
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

        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->post(route('api.likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);

        $this->post(route('api.likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_unlike_a_reply()
    {

        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $reply->likes()->create([
            'user_id' => $user->id,
        ]);

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
}