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

class CreateCategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->image = UploadedFile::fake()->image('avatar.jpg');
    }

    /** @test */
    public function guests_should_not_see_the_form_for_creating_a_new_category()
    {
        $response = $this->get(route('admin.categories.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function unauthorised_users_should_not_see_the_form_for_creating_a_new_category()
    {
        $this->signIn();

        $response = $this->get(route('admin.categories.create'));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_title_is_required()
    {
        $attributes = raw(Category::class);
        unset($attributes['title']);
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_must_be_of_type_string()
    {
        $attributes = raw(Category::class);
        $attributes['title'] = [];
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_must_consist_of_at_least_3_characters()
    {
        $attributes = raw(Category::class);
        $attributes['title'] = 'ab';
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function the_title_must_be_max_100_characters()
    {
        $attributes = raw(Category::class);
        $attributes['title'] = $this->faker()->text(500);
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function an_excerpt_is_required()
    {
        $attributes = raw(Category::class);
        unset($attributes['excerpt']);
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_be_of_type_string()
    {
        $attributes = raw(Category::class);
        $attributes['excerpt'] = [];
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_consist_of_at_least_3_characters()
    {
        $attributes = raw(Category::class);
        $attributes['excerpt'] = 'ab';
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_excerpt_must_be_max_100_characters()
    {
        $attributes = raw(Category::class);
        $attributes['excerpt'] = $this->faker()->text(500);
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('excerpt');
    }

    /** @test */
    public function the_parent_id_must_of_type_int()
    {
        $attributes = raw(Category::class);
        $attributes['parent_id'] = [100];
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('parent_id');
    }

    /** @test */
    public function the_parent_id_must_already_exist_in_the_database_when_is_provided()
    {
        $attributes = raw(Category::class);
        $attributes['parent_id'] = 100;
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('parent_id');
    }

    /** @test */
    public function the_group_category_id_is_required()
    {
        $attributes = raw(Category::class);
        unset($attributes['group_category_id']);
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function the_group_category_id_must_be_of_type_in()
    {
        $attributes = raw(Category::class);
        $attributes['group_category_id'] = [100];
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function the_group_category_id_must_already_exist_in_the_database_when_is_provided()
    {
        $attributes = raw(Category::class);
        $attributes['group_category_id'] = 100;
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function when_parent_id_is_provided_then_the_group_category_id_must_be_the_same_with_group_category_id_of_the_parent()
    {
        $unrelatedGroupCategory = create(GroupCategory::class);
        $parentCategory = create(Category::class);
        $attributes = raw(Category::class, [
            'parent_id' => $parentCategory->id,
            'group_category_id' => $unrelatedGroupCategory->id,
        ]);
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('group_category_id');
    }

    /** @test */
    public function the_image_must_be_of_type_image()
    {
        $attributes = raw(Category::class);
        $attributes['image_path'] = $this->faker()->word();
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('image_path');
    }

    /** @test */
    public function the_image_must_be_a_valid_type()
    {
        $attributes = raw(Category::class);
        $attributes['image_path'] = UploadedFile::fake()->image('avatar.pdf');
        $this->signInAdmin();

        $response = $this->post(route('admin.categories.store'), $attributes);

        $response->assertSessionHasErrors('image_path');

    }

    /** @test */
    public function admins_may_create_a_new_parent_category()
    {
        $groupCategory = create(GroupCategory::class);
        $attributes = raw(Category::class, [
            'parent_id' => '',
            'group_category_id' => $groupCategory->id,
            'image_path' => $this->image,
        ]);
        $this->signInAdmin();

        $this->post(route('admin.categories.store'), $attributes);

        unset($attributes['image_path']);
        $attributes['parent_id'] = null;
        $this->assertDatabaseHas('categories', $attributes);
        $category = Category::where('title', $attributes['title'])->first();
        $this->assertImageExists($category);
    }

    /** @test */
    public function admins_may_create_a_sub_category()
    {
        $groupCategory = create(GroupCategory::class);
        $parentCategory = create(Category::class, ['group_category_id' => $groupCategory->id]);
        $attributes = raw(Category::class, [
            'parent_id' => $parentCategory->id,
            'group_category_id' => $groupCategory->id,
            'image_path' => $this->image,
        ]);
        $this->signInAdmin();

        $this->post(route('admin.categories.store'), $attributes);

        unset($attributes['image_path']);
        $this->assertDatabaseHas('categories', $attributes);
        $category = Category::where('title', $attributes['title'])->first();
        $this->assertImageExists($category);
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
        $groupCategory = create(GroupCategory::class);
        $attributes = raw(Category::class, ['title' => $title]);
        $attributes['parent_id'] = '';
        $attributes['group_category_id'] = $groupCategory->id;
        $attributes['image_path'] = $this->image;

        $this->post(route('admin.categories.store'), $attributes);

        $this->assertEquals(Category::latest('id')->first()->slug, $slug);
    }

    public function assertImageExists($category)
    {
        Storage::disk('public')
            ->assertExists("/images/categories/{$category->id}/image/{$this->image->hashName()}");

        $this->assertEquals(
            asset("/images/categories/{$category->id}/image/{$this->image->hashName()}"),
            $category->fresh()->image_path);
    }
}