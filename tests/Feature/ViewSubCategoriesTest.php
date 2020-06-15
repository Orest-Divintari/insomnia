<?php

namespace Tests\Feature;

use App\Category;
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
        $this->get(route('forum.categories.show', $this->category))
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

        $this->get(route('forum.categories.show', $this->category))
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
        $this->get(route('forum.categories.show', $this->category))
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
        $this->get(route('forum.categories.show', $this->category))
            ->assertSee($repliesTotalCount);
    }
}