<?php

namespace Tests\Feature\Threads;

use Facades\Tests\Setup\ThreadFactory;
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
        $this->signIn();
        $thread = ThreadFactory::withTitle('old title')->create();

        $response = $this->patch(
            route('ajax.threads.update', $thread),
            ['title' => 'new title']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function an_authorized_user_may_update_the_title_of_a_thread()
    {
        $user = $this->signIn();
        $thread = ThreadFactory::by($user)
            ->withTitle('old title')
            ->create();
        $newTitle = [
            'title' => 'new title',
        ];
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'old title',
        ]);

        $this->patch(
            route('ajax.threads.update', $thread),
            $newTitle
        );

        $this->assertDatabaseHas('threads', $newTitle);
    }

    /** @test */
    public function when_updating_a_thread_a_title_is_required()
    {
        $user = $this->signIn();
        $thread = ThreadFactory::by($user)
            ->withTitle('old title')
            ->create();
        $emptyTitle = [
            'title' => '',
        ];
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'old title',
        ]);

        $response = $this->patchJson(
            route('ajax.threads.update', $thread),
            $emptyTitle
        );

        $response->assertStatus(422)
            ->assertJson(['title' => [$this->errorMessage]]);
    }

    /** @test */
    public function when_updating_a_thread_the_title_must_be_of_type_string()
    {
        $user = $this->signIn();
        $thread = ThreadFactory::by($user)
            ->withTitle('old title')
            ->create();
        $nonStringTitle = [
            'title' => array(15),
        ];
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'old title',
        ]);

        $response = $this->patchJson(
            route('ajax.threads.update', $thread),
            $nonStringTitle
        );

        $response->assertStatus(422)
            ->assertJson(['title' => [$this->errorMessage]]);
    }

}
