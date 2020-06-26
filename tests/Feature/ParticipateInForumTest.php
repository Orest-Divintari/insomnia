<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_may_not_post_replies_to_a_thread()
    {
        $thread = create(Thread::class);
        $reply = raw(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $this->post(route('api.replies.store', $thread), $reply)
            ->assertRedirect('login');
    }

    /** @test */
    public function authorized_users_may_post_replies_to_a_thread()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $reply = raw(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $this->post(route('api.replies.store', $thread), $reply);

        $this->assertDatabaseHas('replies', [
            'body' => $reply['body'],
            'repliable_id' => $thread->id,
            'repliable_type' => 'App\Thread',
            'user_id' => $user->id,
        ]);

        // first reply == body of thread
        $this->assertCount(2, $thread->replies);

    }

    /** @test */
    public function a_newly_posted_reply_requires_a_body()
    {
        $this->signIn();
        $thread = create(Thread::class);
        $reply = raw(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
            'body' => '',
        ]);

        $this->post(route('api.replies.store', $thread), $reply)
            ->assertSessionHasErrors('body');

        $this->assertDatabaseMissing('replies', [
            'body' => $reply['body'],
            'repliable_id' => $thread->id,
            'repliable_type' => 'App\Thread',
        ]);
    }

    /** @test */
    public function non_authorized_users_cannot_update_a_reply()
    {
        $this->signIn();

        $reply = create(Reply::class);
        $newBody = ['body' => 'changed body'];
        $this->patch(route('api.replies.update', $reply), $newBody)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authorized_users_may_update_a_reply()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $reply = create(Reply::class, [
            'body' => 'old body',
            'user_id' => $user->id,
        ]);

        $newBody = ['body' => 'changed body'];

        $this->patch(route('api.replies.update', $reply), $newBody);

        $this->assertDatabaseMissing('replies', [
            'body' => 'old body',
            'user_id' => $user->id,
        ]);

        $this->assertDataBaseHas('replies', [
            'body' => $newBody['body'],
            'user_id' => $user->id,

        ]);

    }

    /** @test */
    public function authorized_users_may_delete_a_reply()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'user_id' => $user->id,
            'repliable_id' => $thread->id,
        ]);

        $this->assertEquals($thread->id, $reply->repliable->id);

        $this->delete(route('api.replies.destroy', $reply));

        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id,
            'repliable_id' => $thread->id,
        ]);

        // first reply == body of thread
        $this->assertCount(1, $thread->fresh()->replies);

    }

    /** @test */
    public function non_authorized_users_cannot_delete_a_reply()
    {
        $this->signIn();
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
        ]);

        $this->delete(route('api.replies.destroy', $reply))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
        ]);

    }
}