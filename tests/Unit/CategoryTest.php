<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\GroupCategory;
use App\Models\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_category_has_a_path()
    {
        $category = create(Category::class);

        $this->assertEquals(
            "/forum/categories/{$category->slug}",
            $category->path());
    }

    /** @test  */
    public function a_sub_category_belongs_to_a_category()
    {
        $category = create(Category::class);

        $subCategory = create(
            Category::class,
            ['parent_id' => $category->id]
        );

        $this->assertInstanceOf(
            Category::class,
            $subCategory->category
        );
        $this->assertEquals(
            $category->id,
            $subCategory->category->id
        );
    }

    /** @test */
    public function a_category_may_have_a_sub_category()
    {
        $category = create(Category::class);
        $subCategory = create(
            Category::class,
            ['parent_id' => $category->id]
        );

        $this->assertEquals(
            $subCategory->id,
            $category->subCategories->first()->id
        );
        $this->assertCount(1, $category->subCategories);
    }

    /** @test */
    public function a_root_category_belongs_to_a_group()
    {
        $group = create(GroupCategory::class);
        $category = create(
            Category::class,
            ['group_category_id' => $group->id]
        );

        $this->assertEquals(
            $group->id,
            $category->group->id
        );
        $this->assertInstanceOf(
            GroupCategory::class,
            $category->group
        );
    }

    /** @test */
    public function a_non_parent_category_has_threads()
    {
        $category = create(Category::class);
        $this->assertCount(0, $category->threads);

        createMany(
            Thread::class,
            2,
            ['category_id' => $category->id]
        );

        $this->assertCount(2, $category->fresh()->threads);
    }

    /** @test */
    public function a_category_can_determine_the_path_to_its_avatar()
    {
        $image = '/avatars/categories/apple_logo.png';
        $category = create(Category::class, ['image_path' => $image]);

        $this->assertEquals(asset($image), $category->image_path);
    }

    /** @test */
    public function get_the_recently_active_thread_for_a_non_parent_category()
    {
        $this->withoutExceptionHandling();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $user = $this->signIn();
        $category = create(Category::class);
        $recentThread = ThreadFactory::inCategory($category)->create();
        $oldThread = ThreadFactory::inCategory($category)
            ->updatedAt(Carbon::now()->subMinute())
            ->create();
        $category = Category::where('id', $category->id)
            ->withRecentlyActiveThread()
            ->first();

        $this->assertEquals(
            $recentThread->id,
            $category->recently_active_thread_id
        );

        $category->delete();
        $user->delete();
    }

    /** @test */
    public function get_the_recently_active_thread_for_a_parent_category()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $user = $this->signIn();
        $category = create(Category::class);
        $subCategory = create(
            Category::class,
            ['parent_id' => $category->id]
        );
        $recentThread = create(
            Thread::class,
            ['category_id' => $subCategory->id]
        );
        $oldThread = ThreadFactory::inCategory($subCategory)
            ->updatedAt(Carbon::now()->subMinute())
            ->create();

        $parentCategory = Category::where('id', $category->id)
            ->withRecentlyActiveThread()
            ->first();

        $this->assertEquals(
            $recentThread->id,
            $parentCategory->recently_active_thread_id
        );

        $category->delete();
        $user->delete();
    }

    /** @test */
    public function count_the_threads_that_are_associated_with_a_non_parent_category()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $user = $this->signIn();
        $category = create(Category::class);
        $threadsCount = 2;
        $firstThread = create(Thread::class, ['category_id' => $category->id]);
        $secondThread = create(Thread::class, ['category_id' => $category->id]);

        $category = Category::whereId($category->id)
            ->withThreadsCount()
            ->first();

        $this->assertEquals($threadsCount, $category->threads_count);

        $category->delete();
        $user->delete();
    }

    /** @test */
    public function count_the_threads_that_are_associated_with_a_parent_category_through_its_subcategories()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $user = $this->signIn();
        $category = create(Category::class);
        $subCategory = create(Category::class, ['parent_id' => $category]);
        $threadsCount = 2;
        $firstThread = create(Thread::class, ['category_id' => $subCategory->id]);
        $secondThread = create(Thread::class, ['category_id' => $subCategory->id]);

        $category = Category::whereId($category->id)
            ->withThreadsCount()
            ->first();

        $this->assertEquals($threadsCount, $category->threads_count);

        $category->delete();
        $user->delete();
    }

    /** @test */
    public function count_the_replies_that_are_associated_with_a_non_parent_category()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $user = $this->signIn();
        $category = create(Category::class);
        $repliesCount = 2;
        $firstThread = create(Thread::class, ['category_id' => $category->id]);
        $secondThread = create(Thread::class, ['category_id' => $category->id]);
        $firstThread->increment('replies_count');
        $secondThread->increment('replies_count');

        $category = Category::whereId($category->id)
            ->withRepliesCount()
            ->first();

        $this->assertEquals($repliesCount, $category->replies_count);

        $category->delete();
        $user->delete();
    }

    /** @test */
    public function count_the_replies_that_are_associated_with_a_parent_category_through_its_subcategories()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $user = $this->signIn();
        $category = create(Category::class);
        $subCategory = create(Category::class, ['parent_id' => $category]);
        $repliesCount = 2;
        $firstThread = create(Thread::class, ['category_id' => $subCategory->id]);
        $secondThread = create(Thread::class, ['category_id' => $subCategory->id]);
        $firstThread->increment('replies_count');
        $secondThread->increment('replies_count');

        $category = Category::whereId($category->id)
            ->withThreadsCount()
            ->first();

        $this->assertEquals($repliesCount, $category->threads_count);

        $category->delete();
        $user->delete();
    }

    /** @test */
    public function check_whether_a_category_has_subcategories()
    {
        $category = create(Category::class);
        $this->assertFalse($category->hasSubCategories());

        create(
            Category::class,
            ['parent_id' => $category->id]
        );

        $this->assertTrue($category->fresh()->hasSubCategories());
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
        $category = create(Category::class);
        $this->assertFalse($category->hasThreads());
        createMany(
            Thread::class,
            10,
            [
                'category_id' => $category->id,
            ]
        );

        $this->assertTrue($category->fresh()->hasThreads());
    }

    /** @test */
    public function given_a_category_get_its_tree_of_subcategories()
    {
        $computer = create(Category::class);
        $windows = create(Category::class, ['parent_id' => $computer->id]);
        $mac = create(Category::class, ['parent_id' => $computer->id]);
        $hp = create(Category::class, ['parent_id' => $windows->id]);
        $macbook = create(Category::class, ['parent_id' => $mac->id]);

        $categories = $computer->tree();

        $this->assertCount(4, $categories);
    }

    /** @test */
    public function when_a_parent_category_is_deleted_then_delete_all_its_subcategories()
    {
        $computer = create(Category::class);
        $windows = create(Category::class, ['parent_id' => $computer->id]);
        $mac = create(Category::class, ['parent_id' => $computer->id]);
        $hp = create(Category::class, ['parent_id' => $windows->id]);
        $macbook = create(Category::class, ['parent_id' => $mac->id]);

        $computer->delete();

        $this->assertEquals(0, Category::count());
    }

    /** @test */
    public function when_a_category_is_deleted_then_delete_its_associated_threads()
    {
        $computer = create(Category::class);
        ThreadFactory::inCategory($computer)->createMany(5);
        $computer->delete();

        $this->assertEquals(0, Category::count());
        $this->assertEquals(0, Thread::count());
    }

    /** @test */
    public function it_knows_the_number_of_descendant_categories()
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
        $grandParentCategory = create(Category::class);
        $parentCategory = create(Category::class, ['parent_id' => $grandParentCategory->id]);
        $category = create(Category::class, ['parent_id' => $parentCategory->id]);

        $grandParentCategory = Category::query()
            ->where('id', $grandParentCategory->id)
            ->withDescendantCategoriesCount()
            ->first();

        $this->assertEquals($grandParentCategory->descendant_categories_count, 2);
    }
}