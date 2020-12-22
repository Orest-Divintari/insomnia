<?php

namespace Tests\Feature\Categories;

use App\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupCategoryTest extends TestCase
{
    use RefreshDatabase;

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