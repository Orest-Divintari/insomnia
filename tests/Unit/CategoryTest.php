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
            Category::class,
            ['parent_id' => $this->category->id]
        );

        $this->assertInstanceOf(
            Category::class,
            $subCategory->category
        );
        $this->assertEquals(
            $this->category->id,
            $subCategory->category->id
        );
    }

    /** @test */
    public function a_category_may_have_a_sub_category()
    {
        $subCategory = create(
            Category::class,
            ['parent_id' => $this->category->id]
        );

        $this->assertEquals(
            $subCategory->id,
            $this->category->subCategories->first()->id
        );
        $this->assertCount(1, $this->category->subCategories);
    }

    /** @test */
    public function a_category_belongs_to_a_group()
    {
        $group = create(GroupCategory::class);
        $this->category->update(['group_category_id' => $group->id]);

        $this->assertEquals(
            $group->id,
            $this->category->group->id
        );
        $this->assertInstanceOf(
            GroupCategory::class,
            $this->category->group
        );
    }

    /** @test */
    public function a_non_parent_category_has_threads()
    {
        $this->assertCount(0, $this->category->fresh()->threads);

        createMany(
            Thread::class,
            2,
            ['category_id' => $this->category->id]
        );

        $this->assertCount(2, $this->category->fresh()->threads);
    }

    /** @test */
    public function a_parent_category_has_threads_through_subcategories()
    {
        $subCategory = create(
            Category::class,
            ['parent_id' => $this->category->id]
        );
        dd($this->category->isRoot());
        // $this->category->parentCategoryThreads
        createMany(
            Thread::class,
            2,
            ['category_id' => $subCategory->id]
        );

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
        create(Thread::class, [
            'category_id' => $this->category->id,
            'updated_at' => Carbon::now()->subMinute(),
        ]);
        $category = Category::where('id', $this->category->id)
            ->withRecentActiveThread()
            ->first();

        $this->assertEquals(
            $recentThread->id,
            $category->recently_active_thread_id
        );
    }

    /** @test */
    public function a_parent_category_has_a_recently_active_thread()
    {
        $subCategory = create(
            Category::class,
            ['parent_id' => $this->category->id]
        );
        $recentThread = create(
            Thread::class,
            ['category_id' => $subCategory->id]
        );
        $oldThread = create(
            Thread::class,
            [
                'category_id' => $subCategory->id,
                'updated_at' => Carbon::now()->subMinute(),
            ]
        );
        $parentCategory = Category::where('id', $this->category->id)
            ->withParentRecentActiveThread()
            ->first();

        $this->assertEquals(
            $recentThread->id,
            $parentCategory->parent_category_recently_active_thread_id
        );
    }

    /** @test */
    public function check_whether_a_category_has_subcategories()
    {
        $this->assertFalse($this->category->hasSubCategories());
        create(
            Category::class,
            ['parent_id' => $this->category->id]
        );

        $this->assertTrue($this->category->fresh()->hasSubCategories());
    }

    /** @test */
    public function check_whether_it_is_a_root_category()
    {
        $category = create(Category::class);

        $this->assertTrue($category->isRoot());
    }

    /** @test */
    public function a_category_knows_if_it_has_threads()
    {
        $this->assertFalse($this->category->hasThreads());
        createMany(
            Thread::class,
            10,
            [
                'category_id' => $this->category->id,
            ]
        );

        $this->assertTrue($this->category->fresh()->hasThreads());
    }
}