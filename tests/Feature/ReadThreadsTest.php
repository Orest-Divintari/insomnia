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
    public function a_user_can_view_a_single_thread()
    {

        $response = $this->get($this->thread->api_path())->json();
        $this->assertEquals($this->thread->title, $response['data']['title']);
    }

    /** @test */
    public function a_user_can_view_the_replies_associated_with_a_thread()
    {
        $firstReply = create(Reply::class, [
            'repliable_id' => $this->thread->id,
            'repliable_type' => Thread::class,
        ]);
        $secondReply = create(Reply::class, [
            'repliable_id' => $this->thread->id,
            'repliable_type' => Thread::class,
        ]);
        $response = $this->get(route('replies.index', ['thread' => $this->thread->slug]))->json();
        $this->assertCount(2, $response['data']);
    }

    /** @test */
    public function a_user_can_view_the_threads_associated_with_a_category()
    {
        $category = create(Category::class);
        $thread = create(Thread::class, ['category_id' => $category->id]);
        $this->get('/categories/' . $category->slug . '/threads')
            ->assertSee($thread->title);
    }

}