<?php

namespace Tests\Feature;

use App\Category;
use App\Reply;
use App\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewSubCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->category = create(Category::class);
        $this->subCategory = create(Category::class, [
            'parent_id' => $this->category->id,
        ]);
    }

    /** @test */
    public function a_user_can_view_sub_categories_of_a_category()
    {
        $this->get(route('categories.show', $this->category))
            ->assertSee($this->subCategory->title);
    }

    /** @test */
    public function a_user_can_view_the_most_recently_active_thread_of_a_sub_category()
    {
        $recentThread = create(Thread::class, [
            'category_id' => $this->subCategory->id,
        ]);

        $oldThread = create(Thread::class, [
            'category_id' => $this->subCategory->id,
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $this->get(route('categories.show', $this->category))
            ->assertSee($recentThread->short_title)
            ->assertDontSee($oldThread->short_title);
    }

    /** @test */
    public function a_user_can_view_the_total_number_of_threads_associated_with_a_sub_category()
    {
        $threadsCount = 5;
        createMany(Thread::class, $threadsCount, [
            'category_id' => $this->subCategory->id,
        ]);
        $this->get(route('categories.show', $this->category))
            ->assertSee($threadsCount);
    }

    /** @test */
    public function a_user_can_view_the_total_number_of_replies_associated_with_a_sub_category()
    {
        $threadsCount = 5;
        $repliesCount = 3;
        $repliesTotalCount = $repliesCount * $threadsCount;

        createMany(Thread::class, $threadsCount, [
            'category_id' => $this->subCategory->id,
            'replies_count' => $repliesCount,
        ]);
        $this->get(route('categories.show', $this->category))
            ->assertSee($repliesTotalCount);
    }

    /** @test */
    public function a_user_can_view_the_user_name_who_posted_the_most_recent_reply()
    {
        $thread = create(Thread::class, [
            'category_id' => $this->subCategory->id,
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $this->get(route('categories.show', $this->category))
            ->assertSee($thread->replies->first()->poster->shortName);

    }

    /** @test */
    public function a_user_can_view_the_name_of_the_user_who_created_the_most_recent_thread()
    {
        $recentThread = create(Thread::class, [
            'category_id' => $this->subCategory->id,
        ]);

        $oldThread = create(Thread::class, [
            'category_id' => $this->subCategory->id,
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $this->get(route('categories.show', $this->category))
            ->assertSee($recentThread->poster->shortName)
            ->assertDontSee($oldThread->poster->shortName);
    }
}