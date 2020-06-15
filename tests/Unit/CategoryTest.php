<?php

namespace Tests\Unit;

use App\Category;
use App\GroupCategory;
use App\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up category test
     *
     * Create a Category instance everytime we run a test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->category = create(Category::class);
    }

    /** @test */
    public function a_category_has_a_path()
    {

        $this->assertEquals(
            "/forum/categories/{$this->category->slug}",
            $this->category->path());
    }

    /** @test  */
    public function a_sub_category_belongs_to_a_category()
    {

        $subCategory = create(
            Category::class, [
                'parent_id' => $this->category->id,
            ]);
        $this->assertInstanceOf(Category::class, $subCategory->category);
        $this->assertEquals($this->category->id, $subCategory->category->id);
    }

    /** @test */
    public function a_category_may_have_a_sub_category()
    {
        $subCategory = create(
            Category::class, [
                'parent_id' => $this->category->id,
            ]);
        $this->assertCount(1, $this->category->subCategories);
    }

    /** @test */
    public function a_category_belongs_to_a_group()
    {
        $group = create(GroupCategory::class);
        $this->category->update(['group_category_id' => $group->id]);

        $this->assertInstanceOf(GroupCategory::class, $this->category->group);
    }

    /** @test */
    public function a_non_parent_category_has_threads()
    {
        $threads = createMany(Thread::class, 2, ['category_id' => $this->category->id]);
        $this->assertCount(2, $this->category->threads);
    }

    /** @test */
    public function a_parent_category_has_threads_through_subcategories()
    {
        $subCategory = create(Category::class, [
            'parent_id' => $this->category->id,
        ]);

        createMany(Thread::class, 2, [
            'category_id' => $subCategory->id,
        ]);

        $this->assertCount(2, $this->category->parentCategoryThreads);

    }

    /** @test */
    public function a_category_can_determine_the_path_to_its_avatar()
    {
        $avatar = '/avatars/categories/apple_logo.png';

        $this->category->update([
            'avatar_path' => '/avatars/categories/apple_logo.png',
        ]);

        $this->assertEquals(asset($avatar), $this->category->avatar_path);
    }

    /** @test */
    public function a_non_parent_category_has_a_recently_active_thread()
    {
        $recentThread = create(Thread::class, [
            'category_id' => $this->category->id,
        ]);

        $oldThread = create(Thread::class, [
            'category_id' => $this->category->id,
            'updated_at' => Carbon::now()->subMinute(),
        ]);

        $this->assertEquals($recentThread->id, $this->category
                ->recentlyActiveThread
                ->id
        );
    }

    /** @test */
    public function a_parent_category_has_a_recently_active_thread()
    {
        $subCategory = create(Category::class, [
            'parent_id' => $this->category->id,
        ]);

        $recentThread = create(Thread::class, [
            'category_id' => $subCategory->id,
        ]);

        $oldThread = create(Thread::class, [
            'category_id' => $subCategory->id,
            'updated_at' => Carbon::now()->subMinute(),
        ]);

        $this->assertEquals($recentThread->id, $this->category
                ->parentCategoryRecentlyActiveThread
                ->id
        );
    }

    /** @test */
    public function a_category_has_the_total_number_of_replies_associated_with_it()
    {
        createMany(Thread::class, 2, [
            'category_id' => $this->category->id,
            'replies_count' => 5,
        ]);

        dd($this->category->fresh()->toArray());
    }

    /** @test */
    public function check_whether_a_category_has_subcategories()
    {
        $this->assertFalse($this->category->hasSubCategories());

        $subCategory = create(Category::class, [
            'parent_id' => $this->category->id,
        ]);

        $this->assertTrue($this->category->fresh()->hasSubCategories());
    }
}