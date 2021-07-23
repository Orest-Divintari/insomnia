<?php

namespace Tests\Feature\Admin\GroupCategories;

use App\Models\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateGroupCategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function guests_should_not_see_the_form_to_edit_a_group_category()
    {
        $groupCategory = create(GroupCategory::class);

        $response = $this->get(route('admin.group-categories.edit', $groupCategory));

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_should_not_see_the_form_to_edit_a_group_category()
    {
        $unathorizedUser = $this->signIn();
        $groupCategory = create(GroupCategory::class);

        $response = $this->get(route('admin.group-categories.edit', $groupCategory));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function an_admin_may_see_the_form_to_edit_a_group_category()
    {
        $admin = $this->signInAdmin();
        $groupCategory = create(GroupCategory::class);

        $response = $this->get(route('admin.group-categories.edit', $groupCategory));

        $response->assertOk()
            ->assertViewHas('groupCategory', $groupCategory);
    }

    /** @test */
    public function an_admin_may_update_the_title()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $title = $this->faker()->sentence();
        $excerpt = $groupCategory->excerpt;

        $this->patch(route('admin.group-categories.update', $groupCategory), compact('title', 'excerpt'));

        $this->assertEquals($groupCategory->fresh()->title, $title);
    }

    /** @test */
    public function an_admin_may_update_the_excerpt()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $excerpt = $this->faker()->sentence();
        $title = $groupCategory->title;

        $this->patch(route('admin.group-categories.update', $groupCategory), compact('title', 'excerpt'));

        $this->assertEquals($groupCategory->fresh()->excerpt, $excerpt);
    }

    /** @test */
    public function a_title_is_required_in_order_to_update_a_group_category()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $excerpt = $this->faker()->sentence();

        $response = $this->patch(route('admin.group-categories.update', $groupCategory), compact('excerpt'));

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_title_must_consist_of_at_least_three_characters()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $title = 'ab';
        $excerpt = $this->faker()->sentence();

        $response = $this->patch(route('admin.group-categories.update', $groupCategory), compact('title', 'excerpt'));

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_title_must_be_max_100_characters()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $title = $this->faker()->text(150);
        $excerpt = $this->faker()->sentence();

        $response = $this->patch(route('admin.group-categories.update', $groupCategory), compact('title', 'excerpt'));

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_excerpt_must_consist_of_at_least_three_characters()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $excerpt = 'ab';
        $title = $this->faker()->sentence();

        $response = $this->patch(route('admin.group-categories.update', $groupCategory), compact('title', 'excerpt'));

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_be_max_100_characters()
    {
        $groupCategory = create(GroupCategory::class);
        $admin = $this->signInAdmin();
        $excerpt = $this->faker()->text(150);
        $title = $this->faker()->sentence();

        $response = $this->patch(route('admin.group-categories.update', $groupCategory), compact('title', 'excerpt'));

        $response->assertSessionHasErrors('excerpt');
    }
}
