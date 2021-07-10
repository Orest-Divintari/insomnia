<?php

namespace Tests\Feature\Admin\Categories;

use App\Category;
use App\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateCategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function guests_should_not_see_the_form_for_updating_a_category()
    {
        $category = create(Category::class);

        $response = $this->get(route('admin.categories.edit', $category));

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorised_users_should_not_see_the_form_for_updating_a_category()
    {
        $this->signIn();
        $category = create(Category::class);

        $response = $this->get(route('admin.categories.edit', $category));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function admins_may_see_the_form_for_updating_a_category()
    {
        $this->signInAdmin();
        $parentCategory = create(Category::class);
        $category = create(Category::class, [
            'parent_id' => $parentCategory->id,
            'group_category_id' => $parentCategory->group->id,
        ]);

        $response = $this->get(route('admin.categories.edit', $category));

        $response->assertSee($category->title)
            ->assertSee($category->excerpt)
            ->assertSee($category->category->title)
            ->assertSee($category->group->title);
    }

    /** @test */
    public function a_title_is_required()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        unset($attributes['title']);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_must_be_of_type_string()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['title'] = [];
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_must_consist_of_at_least_3_characters()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['title'] = 'ab';
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_must_be_max_100_characters()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['title'] = $this->faker()->text(500);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function an_excerpt_is_required()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        unset($attributes['excerpt']);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_be_of_type_string()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['excerpt'] = [];
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_consist_of_at_least_3_characters()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['excerpt'] = 'ab';
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_be_max_100_characters()
    {
        $category = create(Category::class);
        $attribX4utes = raw(Category::class);
        $attributes['excerpt'] = $this->faker()->text(500);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_parent_id_must_be_int()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['parent_id'] = [100];
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('parent_id');
    }

    /** @test */
    public function the_parent_id_must_already_exist_in_the_database_when_is_provided()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['parent_id'] = 100;
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('parent_id');
    }

    /** @test */
    public function the_group_category_id_is_required()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        unset($attributes['group_category_id']);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function the_group_category_id_must_be_of_type_in()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['group_category_id'] = [100];
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function the_group_category_id_must_already_exist_in_the_database_when_is_provided()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['group_category_id'] = 100;
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function when_parent_id_is_provided_then_the_group_category_id_must_be_the_same_with_group_category_id_of_the_parent()
    {
        $unrelatedGroupCategory = create(GroupCategory::class);
        $category = create(Category::class);
        $newParentCategory = create(Category::class);
        $attributes = raw(Category::class, [
            'parent_id' => $newParentCategory->id,
            'group_category_id' => $unrelatedGroupCategory->id,
        ]);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function the_image_must_be_of_type_image()
    {
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['image_path'] = $this->faker()->word();
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('image_path');
    }

    /** @test */
    public function the_image_must_be_a_valid_type()
    {
        Storage::fake('public');
        $category = create(Category::class);
        $attributes = raw(Category::class);
        $attributes['image_path'] = UploadedFile::fake()->image('avatar.pdf');
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertSessionHasErrors('image_path');

    }

    /** @test */
    public function admins_may_update_a_category()
    {
        Storage::fake('public');
        $parentCategory = create(Category::class);
        $category = create(Category::class);
        $image = UploadedFile::fake()->image('image.jpg');
        $attributes = raw(Category::class, [
            'parent_id' => $parentCategory->id,
            'group_category_id' => $parentCategory->group->id,
            'image_path' => $image,
        ]);
        $this->signInAdmin();

        $response = $this->patch(route('admin.categories.update', $category), $attributes);

        $response->assertRedirect(route('admin.categories.index'));
        unset($attributes['image_path']);
        $this->assertDatabaseHas('categories', $attributes);
        $category = Category::where('title', $attributes['title'])->first();
        $this->assertImageExists($category, $image);
        $this->assertPreviousImageDoesNotExist($category);
    }

    /** @test */
    public function a_category_requires_a_unique_slug()
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
        Storage::fake('public');
        $image = UploadedFile::fake()->image('image.jpg');
        $category = create(Category::class);
        $groupCategory = create(GroupCategory::class);
        $attributes = raw(Category::class, [
            'title' => $title,
            'parent_id' => '',
            'group_category_id' => $groupCategory->id,
            'image_path' => $image,
        ]);

        $this->patch(route('admin.categories.update', $category), $attributes);

        $this->assertEquals($category->fresh()->slug, $slug);
    }

    public function assertImageExists($category, $image)
    {
        Storage::disk('public')
            ->assertExists("/images/categories/{$category->id}/image/{$image->hashName()}");

        $this->assertEquals(
            asset("/images/categories/{$category->id}/image/{$image->hashName()}"),
            $category->fresh()->image_path);
    }

    protected function assertPreviousImageDoesNotExist($category)
    {
        Storage::disk('public')
            ->assertMissing($category->image_path);
    }

}