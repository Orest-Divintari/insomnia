<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_authorized_users_may_not_update_the_title_of_a_thread()
    {
        $this->signIn();
        $thread = create(Thread::class, [
            'title' => 'old title',
        ]);

        $this->put(route('api.threads.update', $thread), [])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function an_authorized_user_may_update_the_title_of_a_thread()
    {
        $user = $this->signIn();

        $thread = create(Thread::class, [
            'title' => 'old title',
            'user_id' => $user->id,
        ]);

        $newTitle = [
            'title' => 'new title',
        ];
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'old title',
        ]);

        $this->put(route('api.threads.update', $thread), $newTitle);

        $this->assertDatabaseHas('threads', $newTitle);

    }

}