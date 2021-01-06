<?php

namespace Tests\Feature\Categories;

use App\Category;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewSubCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
    }

    /** @test */
    public function a_user_can_view_the_list_of_subcategories_of_a_parent_category()
    {
        $mac = create(Category::class);
        $macbook = create(Category::class, ['parent_id' => $mac->id]);
        $imac = create(Category::class, ['parent_id' => $mac->id]);

        $response = $this->get(route('categories.show', $mac));

        $response->assertSee($macbook->title)
            ->assertSee($imac->title);

        $mac->delete();
    }

    /** @test */
    public function a_user_can_view_the_number_of_threads_and_replies_associated_with_a_sub_category()
    {
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
        $repliesCount = 10;
        $threadsCount = 2;
        create(Thread::class, ['replies_count' => 5, 'category_id' => $ios13->id]);
        create(Thread::class, ['replies_count' => 5, 'category_id' => $ios13->id]);

        $response = $this->get(route('categories.show', $ios));

        $response->assertSee($repliesCount)
            ->assertSee($threadsCount);

        $software->delete();
        $user->delete();
    }

    /** @test */
    public function a_user_can_view_the_most_recently_active_thread_of_a_sub_category()
    {
        $user = $this->signIn();
        $ios = create(Category::class);
        $ios13 = create(
            Category::class,
            ['parent_id' => $ios->id]
        );
        $ios14 = create(
            Category::class,
            ['parent_id' => $ios->id]
        );

        $recentlyActiveIos13Thread = create(Thread::class,
            ['category_id' => $ios13->id]
        );
        $recentlyActiveIos14Thread = create(
            Thread::class,
            ['category_id' => $ios14->id]
        );
        Carbon::setTestNow(Carbon::now()->subDay());
        $oldIos13Thread = create(
            Thread::class,
            ['category_id' => $ios13->id]
        );
        $oldIos14Thread = create(
            Thread::class,
            ['category_id' => $ios14->id]
        );
        Carbon::setTestNow();

        $response = $this->get(route('categories.show', $ios));

        $response->assertSee($recentlyActiveIos13Thread->shortTitle)
            ->assertSee($recentlyActiveIos14Thread->shortTitle);

        $ios->delete();
        $user->delete();
    }

    /** @test */
    public function a_user_can_view_the_the_poster_of_the_most_recent_reply_of_a_sub_category()
    {
        $recentIosReplyPoster = create(User::class);
        $oldIosReplyPoster = create(User::class);
        $ios = create(Category::class);
        $ios13 = create(
            Category::class,
            ['parent_id' => $ios->id]
        );
        create(
            Thread::class,
            [
                'user_id' => $recentIosReplyPoster->id,
                'category_id' => $ios13->id,
            ]
        );
        Carbon::setTestNow(Carbon::now()->subDay());
        create(
            Thread::class,
            [
                'user_id' => $oldIosReplyPoster->id,
                'category_id' => $ios13->id,
            ]
        );
        Carbon::setTestNow();

        $response = $this->get(route('forum'));

        $response->assertSee($recentIosReplyPoster->shortName);

        $ios->delete();
        $recentIosReplyPoster->delete();
        $oldIosReplyPoster->delete();
    }

    /** @test */
    public function view_the_list_of_the_associated_threads_of_a_sub_category_that_has_no_descendants()
    {
        $user = $this->signIn();
        $accessories = create(Category::class);
        $threadAccessories = create(
            Thread::class,
            ['category_id' => $accessories->id]
        );

        $response = $this->get(route('categories.show', $accessories));

        $response->assertRedirect(route('threads.index', $accessories));
    }
}