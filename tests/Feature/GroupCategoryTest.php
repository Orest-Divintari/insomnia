<?php

namespace Tests\Feature;

use App\Category;
use App\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_group_has_categories()
    {
        $group = create(GroupCategory::class);
        $category = create(Category::class, ['group_category_id' => $group->id]);
        $this->assertCount(1, $group->categories);
    }

    /** @test */
    public function fetch_the_group_together_with_the_parent_categories()
    {
        $group = create(GroupCategory::class);
        $parentCategory = create(Category::class, ['group_category_id' => $group->id]);

        create(Category::class, ['parent_id' => $parentCategory->id, 'group_category_id' => $group->id]);

        $this->assertCount(1, $group->withCategories());
    }
}