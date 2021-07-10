<?php

namespace Tests\Feature\Admin\Categories;

use App\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewCategoriesTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function guests_should_not_view_the_categories_list()
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorised_users_should_not_view_the_categories_list()
    {
        $this->signIn();

        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function admins_may_view_the_categories_list()
    {
        $category = create(Category::class);
        $this->signInAdmin();

        $response = $this->get(route('admin.categories.index'));

        $response->assertSee($category->title);
    }
}