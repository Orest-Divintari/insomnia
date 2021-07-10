<?php

namespace Tests\Feature\Admin\GroupCategories;

use App\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateGroupCategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function an_admin_can_view_the_form_for_creating_a_new_group_category()
    {
        $admin = $this->signInAdmin();

        $response = $this->get(route('admin.group-categories.create'));

        $response->assertSee('Group categories');
    }

    /** @test */
    public function guests_should_not_see_the_form_for_creating_a_new_group_category()
    {
        $response = $this->get(route('admin.group-categories.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorised_users_should_not_see_the_form_for_creating_a_new_group_category()
    {
        $unathorisedUser = $this->signIn();

        $response = $this->get(route('admin.group-categories.create'));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function an_admin_can_create_a_new_group_category()
    {
        $this->withoutExceptionHandling();
        $admin = $this->signInAdmin();
        $title = $this->faker()->sentence();
        $excerpt = $this->faker()->sentence();

        $response = $this->post(route('admin.group-categories.store', compact('title', 'excerpt')));

        $this->assertDatabaseHas('group_categories', compact('title', 'excerpt'));
        $response->assertRedirect(route('admin.group-categories.index'));
    }

    /** @test */
    public function a_title_is_required_in_order_to_create_a_group_category()
    {
        $admin = $this->signInAdmin();
        $excerpt = $this->faker()->sentence();

        $response = $this->post(route('admin.group-categories.store', compact('excerpt')));

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function an_excerpt_is_required_in_order_to_create_a_group_category()
    {
        $admin = $this->signInAdmin();
        $title = $this->faker()->sentence();

        $response = $this->post(route('admin.group-categories.store', compact('title')));

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function a_title_must_consist_of_at_least_three_characters()
    {
        $admin = $this->signInAdmin();
        $title = 'ab';
        $excerpt = $this->faker()->sentence();

        $response = $this->post(route('admin.group-categories.store', compact('title', 'excerpt')));

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_title_must_be_max_100_characters()
    {
        $admin = $this->signInAdmin();
        $title = $this->faker()->text(500);
        $excerpt = $this->faker()->sentence();

        $response = $this->post(route('admin.group-categories.store', compact('title', 'excerpt')));

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function an_excerpt_must_consist_of_at_least_three_characters()
    {
        $admin = $this->signInAdmin();
        $title = $this->faker()->sentence();
        $excerpt = 'ab';

        $response = $this->post(route('admin.group-categories.store', compact('title', 'excerpt')));

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function an_excerpt_must_be_max_100_characters()
    {
        $admin = $this->signInAdmin();
        $title = $this->faker()->sentence();
        $excerpt = $this->faker()->text(500);

        $response = $this->post(route('admin.group-categories.store', compact('title', 'excerpt')));

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function a_group_category_requires_a_unique_slug()
    {
        $user = $this->signInAdmin();
        $this->assertUniqueSlug('some title', 'some-title');
        $this->assertUniqueSlug('some title', 'some-title.2');
        $this->assertUniqueSlug('some title', 'some-title.3');
        $this->assertUniqueSlug('some title 55', 'some-title-55');
        $this->assertUniqueSlug('some title 55', 'some-title-55.2');
    }

    public function assertUniqueSlug($title, $slug)
    {
        $groupCategory = raw(GroupCategory::class, [
            'title' => $title,
            'excerpt' => $this->faker()->sentence(),
        ]);

        $this->post(route('admin.group-categories.store'), $groupCategory);

        $this->assertEquals(GroupCategory::latest('id')->first()->slug, $slug);
    }
}