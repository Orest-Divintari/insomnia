<?php

namespace Tests\Feature;

use App\Category;
use App\GroupCategory;
use App\Reply;
use App\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_all_categories_and_their_groups()
    {
        $group = create(GroupCategory::class);

        $parentCategories = createMany(Category::class, 3, [
            'group_category_id' => $group->id,
        ])->pluck('title');

        $this->get(route('forum'))
            ->assertSee($group->title)
            ->assertSee($parentCategories[0])
            ->assertSee($parentCategories[1])
            ->assertSee($parentCategories[2]);
    }

    /** @test */
    public function a_user_can_view_a_sub_category_link_if_exists()
    {
        $category = create(Category::class);
        $subCategory = create(Category::class, ['parent_id' => $category->id]);

        $this->get(route('forum'))
            ->assertSee($subCategory->title);
    }

    /** @test */
    public function a_non_parent_category_is_redirected_to_the_threads_associated_with_it()
    {
        $category = create(Category::class);
        $this->get(route('forum.categories.show', $category))
            ->assertRedirect(route('threads.index', $category->slug));
    }

    /** @test */
    public function a_user_can_view_the_most_recently_active_thread_associated_with_a_parent_category()
    {
        $category = create(Category::class);

        $subCategoryOne = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        $subCategoryTwo = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        $recentThreadOne = create(Thread::class, [
            'category_id' => $subCategoryOne->id,
        ]);

        $recentThreadOne = create(Thread::class, [
            'category_id' => $subCategoryOne->id,
            'updated_at' => Carbon::now()->subMinute(),
        ]);

        $recentThreadTwo = create(Thread::class, [
            'category_id' => $subCategoryTwo->id,
        ]);

        $oldThreadTwo = create(Thread::class, [
            'category_id' => $subCategoryTwo->id,
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $this->get(route('forum'))->assertSee($category->parentCategoryRecentlyActiveThread->shortTitle);
    }

    /** @test */
    public function a_user_can_view_a_recently_active_thread_associated_with_a_non_parent_category()
    {
        $category = create(Category::class);

        $recentThread = create(Thread::class, [
            'category_id' => $category->id,
        ]);

        $oldThread = create(Thread::class, [
            'category_id' => $category->id,
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $this->get(route('forum'))
            ->assertSee($category->recentlyActiveThread->shortTitle);
    }

    /** @test */
    public function a_user_can_can_view_the_total_number_of_threads_associated_with_a_parent_category()
    {
        $category = create(Category::class);

        $subCategoryOne = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        $subCategoryTwo = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        createMany(Thread::class, 2, ['category_id' => $subCategoryOne->id]);
        createMany(Thread::class, 2, ['category_id' => $subCategoryTwo->id]);

        $this->get(route('forum'))->assertSee($category->threads_count);

    }

    /** @test */
    public function a_user_can_can_view_the_total_number_of_threads_associated_with_a_non_parent_category()
    {
        $category = create(Category::class);

        createMany(Thread::class, 2, ['category_id' => $category->id]);

        $this->get(route('forum'))->assertSee($category->threads_count);

    }

    /** @test */
    public function a_user_can_view_the_total_number_of_replies_associated_with_a_parent_category()
    {

        $category = create(Category::class);

        $subCategoryOne = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        $subCategoryTwo = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        $threadOne = create(Thread::class, ['category_id' => $subCategoryOne->id]);
        $threadTwo = create(Thread::class, ['category_id' => $subCategoryTwo->id]);

        createMany(Reply::class, 5, [
            'repliable_id' => $threadOne->id,
            'repliable_type' => Thread::class,
        ]);
        createMany(Reply::class, 5, [
            'repliable_id' => $threadTwo->id,
            'repliable_type' => Thread::class,
        ]);

        $this->get(route('forum'))->assertSee($category->replies_count);
    }

    /** @test */
    public function a_user_can_view_the_total_number_of_replies_associated_with_a_non_parent_category()
    {
        $category = create(Category::class);
        $thread = create(Thread::class, ['category_id' => $category->id]);
        createMany(Reply::class, 5, [
            'repliable_id' => $category->id,
            'repliable_type' => Thread::class,
        ]);
        $this->get(route('forum'))->assertSee($category->replies_count);
    }
}