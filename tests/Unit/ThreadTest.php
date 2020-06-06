<?php

namespace Tests\Unit;

use App\Category;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_thread_has_an_api_path()
    {
        $this->assertEquals("/api/threads/{$this->thread->slug}", $this->thread->api_path());
    }

    /** @test */
    public function a_thread_has_replies()
    {

        create('App\Reply', [
            'repliable_id' => $this->thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->assertCount(1, $this->thread->replies);
        create('App\Reply', [
            'repliable_id' => $this->thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->assertCount(2, $this->thread->fresh()->replies);

    }

    /** @test */
    public function a_thread_is_posted_by_a_user()
    {
        $user = create('App\User');
        $thread = create('App\Thread', ['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $thread->poster);
    }

    /** @test */
    public function a_thread_belongs_to_a_category()
    {
        $category = create(Category::class);
        $thread = create(Thread::class, ['category_id' => $category->id]);
        $this->assertInstanceOf(Category::class, $thread->category);
    }

}