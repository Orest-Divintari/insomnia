<?php

namespace Tests\Feature;

use App\Category;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function guests_may_not_see_the_post_new_thread_form()
    {
        $category = create(Category::class);
        $this->get(route('threads.create', $category))
            ->assertRedirect('login');
    }

    /** @test */
    public function guests_may_not_post_new_threads()
    {
        $this->post(route('threads.store'), [])
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_has_to_verify_the_email_before_posting_a_new_thread()
    {
        $user = create(User::class, [
            'email_verified_at' => null,
        ]);

        $this->signIn($user);
        $this->post(route('threads.store'), [])
            ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function authenticated_users_that_have_confirmed_their_email_may_post_threads()
    {
        $this->signIn();
        $thread = raw(Thread::class);
        $title = ['title' => $thread['title']];
        $response = $this->post(route('threads.store'), $thread);
        $this->assertDatabaseHas('threads', $title);
        $this->get($response->headers->get('location'))
            ->assertSee($title['title']);

    }

    /** @test */
    public function a_reply_is_created_when_a_new_thread_is_created_as_the_body_of_the_thread()
    {
        $user = $this->signIn();
        $thread = raw(Thread::class, [
            'user_id' => $user->id,
        ]);

        $this->post(route('threads.store', $thread));

        $this->assertDatabaseHas('replies', [
            'body' => $thread['body'],
            'user_id' => $thread['user_id'],
        ]);

    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->post_thread(['body' => ''])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->post_thread(['title' => ''])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_category()
    {
        $this->post_thread(['category_id' => ''])
            ->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function a_thread_requires_a_category_that_already_exists_in_the_database()
    {
        $this->post_thread(['category_id' => 12345])
            ->assertSessionHasErrors(('category_id'));
    }

    protected function post_thread($overrides)
    {
        $user = $this->signIn();
        $thread = raw(Thread::class, $overrides + ['user_id' => $user->id]);
        return $this->post(route('threads.store'), $thread);
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