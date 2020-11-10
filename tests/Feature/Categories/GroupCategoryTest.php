<?php

namespace Tests\Feature\Categories;

use App\Category;
use App\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up group categories test
     *
     * Create a group category for every test instance
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->group = create(GroupCategory::class);
    }

    /** @test */
    public function a_group_has_categories()
    {
        $category = create(Category::class, [
            'group_category_id' => $this->group->id,
        ]);

        $subCategory = create(Category::class, [
            'parent_id' => $category->id,
        ]);

        $this->assertCount(1, $this->group->categories);
    }
}