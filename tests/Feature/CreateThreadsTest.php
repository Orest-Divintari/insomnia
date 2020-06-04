<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function guests_may_not_create_threads()
    {
        $this->post(route('threads.store'), [])
            ->assertRedirect('login');
    }

    /** @test */
    public function authenticated_users_may_create_threads()
    {

        $user = $this->signIn();
        $thread = raw(Thread::class, ['user_id' => $user->id]);

        $this->assertDatabaseMissing('threads', $title = ['title' => $thread['title']]);
        $this->post(route('threads.store'), $thread);
        $this->assertDatabaseHas('threads', $title);

    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->threadRequires('body');
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->threadRequires('title');
    }

    /** @test */
    public function a_thread_requires_a_category()
    {
        $this->threadRequires('category_id');
    }

    protected function threadRequires($attribute)
    {
        $user = $this->signIn();
        $thread = raw(Thread::class, ['user_id' => $user->id]);
        unset($thread[$attribute]);
        $this->post(route('threads.store'), $thread)
            ->assertSessionHasErrors($attribute);
    }

    /** @test */
    public function a_thread_requires_a_unique_slug()
    {
        $user = $this->signIn();
        $this->assertUniqueSlug('some title', 'some-title');
        $this->assertUniqueSlug('some title', 'some-title.2');
        $this->assertUniqueSlug('some title', 'some-title.3');
        $this->assertUniqueSlug('some title 55', 'some-title-55');
        $this->assertUniqueSlug('some title 55', 'some-title-55.2');
    }

    public function assertUniqueSlug($title, $slug)
    {
        $thread = raw(Thread::class, [
            'title' => $title,
        ]);
        $this->post(route('threads.store'), $thread);
        $this->assertEquals(Thread::latest('id')->first()->slug, $slug);

    }

}