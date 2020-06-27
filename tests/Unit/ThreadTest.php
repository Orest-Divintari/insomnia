<?php

namespace Tests\Unit;

use App\Category;
use App\Reply;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
        $this->assertEquals(
            "/api/threads/{$this->thread->slug}",
            $this->thread->api_path()
        );
    }

    /** @test */
    public function a_thread_has_replies()
    {
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

    /** @test */
    public function a_thread_has_a_most_recent_reply()
    {
        $newReply = create(Reply::class, [
            'repliable_type' => Thread::class,
            'repliable_id' => $this->thread->id,
        ]);

        $oldReply = create(Reply::class, [
            'repliable_type' => Thread::class,
            'repliable_id' => $this->thread->id,
            'updated_at' => Carbon::now()->subDay(),
        ]);

        $this->assertEquals($this->thread->fresh()->recentReply->id, $newReply->id);

    }

    /** @test */
    public function thread_has_a_shorter_version_of_its_title()
    {
        $this->assertEquals(
            Str::limit($this->thread->title, Thread::TITLE_LENGTH, ''),
            $this->thread->shortTitle
        );
    }

    /** @test */
    public function a_thread_is_updated_when_a_new_reply_is_published()
    {
        $thread = create(Thread::class, [
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $this->assertEquals(
            $thread->fresh()->updated_at,
            $reply->updated_at
        );
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $thread = create(Thread::class);
        $reply = raw(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply($reply);

        $this->assertEquals($thread->fresh()->replies_count, 1);

    }

}