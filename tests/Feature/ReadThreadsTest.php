<?php

namespace Tests\Feature;

use App\Category;
use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get(route('threads.show', $this->thread))
            ->assertSee($this->thread->title);

    }

    /** @test */
    public function a_user_can_read_the_paginated_replies_associated_with_a_thread()
    {
        // first reply is the body of the thread
        createMany(Reply::class, 2, [
            'repliable_id' => $this->thread->id,
            'repliable_type' => Thread::class,
        ]);

        $response = $this->get(route('api.replies.index', $this->thread))->json();
        $this->assertCount(Reply::PER_PAGE, $response['data']);

        $response = $this->get(route('api.replies.index', $this->thread) . '?page=2')->json();
        $this->assertCount(Reply::PER_PAGE, $response['data']);

    }

    /** @test */
    public function a_user_can_read_the_threads_associated_with_a_category()
    {
        $category = create(Category::class);
        $thread = create(Thread::class, ['category_id' => $category->id]);
        $this->get(route('threads.index', $category))
            ->assertSee($thread->title);
    }

    /** @test */
    public function a_user_can_read_the_paginated_threads_associated_with_a_category()
    {
        $this->withoutExceptionHandling();
        $category = create(Category::class);
        createMany(Thread::class, 100, [
            'category_id' => $category->id,
        ]);

        $response = $this->getJson(route('api.threads.index', $category))->json();
        $this->assertCount(Thread::PER_PAGE, $response['data']);

        $response = $this->getJson(route('api.threads.index', $category) . '?page=2')->json();
        $this->assertCount(Thread::PER_PAGE, $response['data']);
    }

}