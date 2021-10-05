<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\GroupCategory;
use App\Models\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_group_has_categories()
    {
        $group = create(GroupCategory::class);
        $category = create(
            Category::class,
            ['group_category_id' => $group->id]
        );

        $this->assertEquals(
            $category->id,
            $group->categories()->first()->id
        );
        $this->assertCount(1, $group->categories);
    }

    /** @test */
    public function eager_load_the_categories_and_sub_categories_for_a_group_and_threads_count_and_replies_count_and_recently_active_thread_for_a_category()
    {
        $this->useMysql();
        $user = $this->signIn();
        $software = create(GroupCategory::class, ['title' => 'software']);
        $ios = create(
            Category::class,
            ['group_category_id' => $software->id]
        );
        $ios13 = create(
            Category::class,
            ['parent_id' => $ios->id]
        );
        $macos = create(
            Category::class,
            ['group_category_id' => $software->id]
        );
        create(Thread::class, ['replies_count' => 5, 'category_id' => $ios13->id]);
        create(Thread::class, ['replies_count' => 5, 'category_id' => $macos->id]);
        Carbon::setTestNow(Carbon::now()->addMinutes(5));
        $ios13Thread = create(Thread::class, ['category_id' => $ios13->id, 'title' => 'ios13Thread']);
        $catalinaThread = create(Thread::class, ['category_id' => $macos->id, 'title' => 'catalinaThread']);
        $ios13RecentReply = $ios13Thread->replies()->first();
        Carbon::setTestNow();

        $groups = GroupCategory::whereId($software->id)
            ->withCategories()
            ->get()
            ->toArray()[0];

        $this->assertEquals(
            $groups['categories'][0]['id'],
            $ios->id
        );
        $this->assertEquals(
            $groups['categories'][1]['id'],
            $macos->id
        );
        $this->assertEquals(
            $groups['categories'][0]['sub_categories'][
                0]['id'], $ios13->id
        );
        $this->assertEquals(
            $groups['categories'][0]['recently_active_thread_id'],
            $ios13Thread->id
        );
        $this->assertEquals(
            $ios13RecentReply->id,
            $groups['categories'][0]['recently_active_thread']['recent_reply']['id']
        );
        $this->assertEquals(
            $user->id,
            $groups['categories'][0]['recently_active_thread']['recent_reply']['poster']['id']
        );
        $this->assertEquals(
            $groups['categories'][1]['recently_active_thread_id'],
            $catalinaThread->id
        );
        $this->assertEquals(
            $groups['categories'][1]['threads_count'],
            2
        );
        $this->assertEquals(
            $groups['categories'][0]['threads_count'],
            2
        );
        $this->assertEquals(
            $groups['categories'][0]['replies_count'],
            5
        );
        $this->assertEquals(
            $groups['categories'][1]['replies_count'],
            5
        );

        $software->delete();
        auth()->user()->delete();
    }

    /** @test */
    public function when_a_group_is_deleted_the_associated_categories_are_deleted()
    {
        $group = create(GroupCategory::class);
        $category = create(
            Category::class,
            ['group_category_id' => $group->id]
        );

        $group->delete();

        $this->assertEquals(0, Category::count());
    }

}