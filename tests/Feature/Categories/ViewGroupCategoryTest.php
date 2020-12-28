<?php

namespace Tests\Feature\Categories;

use App\Category;
use App\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewGroupCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function display_all_the_group_categories_and_the_categories_of_each_group()
    {
        $groupCategory = create(GroupCategory::class);
        $category = create(
            Category::class,
            ['group_category_id' => $groupCategory->id]
        );
        $response = $this->get(route('forum'));

        $response->assertSee($groupCategory->title)
            ->assertSee($category->title);
    }
}