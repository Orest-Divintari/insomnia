<?php

namespace Tests\Feature\Admin\GroupCategories;

use App\Models\GroupCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewGroupCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_may_view_the_list_of_group_categories()
    {
        $admin = $this->signInAdmin();
        $groupCategories = createMany(GroupCategory::class, 2);

        $response = $this->get(route('admin.group-categories.index'));

        $response->assertSee($groupCategories->first()->title);
        $response->assertSee($groupCategories->first()->excerpt);
        $response->assertSee($groupCategories->last()->title);
        $response->assertSee($groupCategories->last()->excerpt);
    }
}
