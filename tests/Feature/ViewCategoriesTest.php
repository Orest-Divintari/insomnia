<?php

namespace Tests\Feature;

use App\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_all_categories()
    {
        $categories = createMany(Category::class, 3)->pluck('title');

        $this->get('/forum/categories')
            ->assertSee($categories[0])
            ->assertSee($categories[1])
            ->assertSee($categories[2]);
    }

    /** @test */
    public function a_user_can_view_a_subcategory_if_exists()
    {
        $category = create(Category::class);
        $subCategory = create(Category::class, ['parent_id' => $category->id]);

        $this->get($category->path())
            ->assertSee($subCategory->title);
    }

    /** @test */
    public function a_user_is_redirected_to_the_threads_list_associated_with_a_category_if_subecategory_does_not_exist()
    {
        $this->withExceptionHandling();
        $category = create(Category::class);
        $this->get($category->path())
            ->assertRedirect(route('threads.index', $category->slug));
    }

}