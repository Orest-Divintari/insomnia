<?php

namespace Tests\Feature\Threads;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid title.';
    /** @test */
    public function non_authorized_users_may_not_update_the_title_of_a_thread()
    {
        $thread = create(Thread::class, [
            'title' => 'old title',
        ]);
        $this->signIn();

        $response = $this->patch(
            route('api.threads.update', $thread),
            ['title' => 'new title']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
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

        $this->patch(
            route('api.threads.update', $thread),
            $newTitle
        );

        $this->assertDatabaseHas('threads', $newTitle);
    }

    /** @test */
    public function when_updating_a_thread_a_title_is_required()
    {
        $user = $this->signIn();
        $thread = create(Thread::class, [
            'title' => 'old title',
            'user_id' => $user->id,
        ]);
        $emptyTitle = [
            'title' => '',
        ];
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'old title',
        ]);

        $response = $this->patchJson(
            route('api.threads.update', $thread),
            $emptyTitle
        );

        $response->assertStatus(422)
            ->assertJson(['title' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_thread_the_title_must_be_of_type_string()
    {
        $user = $this->signIn();
        $thread = create(Thread::class, [
            'title' => 'old title',
            'user_id' => $user->id,
        ]);
        $nonStringTitle = [
            'title' => array(15),
        ];
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'old title',
        ]);

        $response = $this->patchJson(
            route('api.threads.update', $thread),
            $nonStringTitle
        );

        $response->assertStatus(422)
            ->assertJson(['title' => [$this->errorMessage]]);
    }

}