<?php

namespace Tests\Feature;

use App\Category;
use App\GroupCategory;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => config('insomnia.database.name')]);
    }

    /** @test */
    public function a_user_can_view_all_root_categories_with_their_subcategories_and_their_groups()
    {
        $software = create(GroupCategory::class, ['title' => 'software']);
        $ios = create(
            Category::class,
            ['group_category_id' => $software->id]
        );
        $macos = create(
            Category::class,
            ['group_category_id' => $software->id]
        );
        $ios13 = create(
            Category::class,
            ['parent_id' => $ios->id]
        );
        $catalina = create(
            Category::class,
            ['parent_id' => $macos->id]
        );

        $response = $this->get(route('forum'));

        $response->assertSee($software->title)
            ->assertSee($ios->title)
            ->assertSee($macos->title)
            ->assertSee($ios13->title)
            ->assertSee($catalina->title);

        $software->delete();
    }

    /** @test */
    public function a_user_can_view_the_most_recently_active_thread_associated_with_a_root_category()
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
        $macos = create(
            Category::class,
            ['group_category_id' => $software->id]
        );
        $recentlyActiveIos13Thread = create(Thread::class,
            ['category_id' => $ios13->id]
        );
        $recentlyActiveMacosThread = create(
            Thread::class,
            ['category_id' => $macos->id]
        );
        Carbon::setTestNow(Carbon::now()->subDay());
        $oldIos13Thread = create(
            Thread::class,
            ['category_id' => $ios13->id]
        );
        $oldMacOsThread = create(
            Thread::class,
            ['category_id' => $macos->id]
        );
        Carbon::setTestNow();

        $response = $this->get(route('forum'));

        $response->assertSee($recentlyActiveIos13Thread->shortTitle)
            ->assertSee($recentlyActiveMacosThread->shortTitle);

        $software->delete();
        $user->delete();
    }

    /** @test */
    public function a_user_can_view_the_number_of_replies_and_threads_associated_with_a_root_category()
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
        $macos = create(
            Category::class,
            ['group_category_id' => $software->id]
        );
        create(Thread::class, ['replies_count' => 5, 'category_id' => $ios13->id]);
        create(Thread::class, ['replies_count' => 5, 'category_id' => $macos->id]);

        $response = $this->get(route('forum'));

        $response->assertSee(5)
            ->assertSee(1);

        $software->delete();
        $user->delete();
    }

    /** @test */
    public function a_user_can_view_the_the_poster_of_the_most_recent_reply_of_a_root_category()
    {
        $macOsReplyPoster = create(User::class);
        $iosReplyPoster = create(User::class);
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
        create(
            Thread::class,
            [
                'user_id' => $iosReplyPoster->id,
                'category_id' => $ios13->id,
            ]
        );
        create(
            Thread::class,
            [
                'user_id' => $macOsReplyPoster->id,
                'category_id' => $macos->id,
            ]
        );

        $response = $this->get(route('forum'));

        $response->assertSee($iosReplyPoster->shortName)
            ->assertSee($macOsReplyPoster->shortName);

        $software->delete();
        $iosReplyPoster->delete();
        $macOsReplyPoster->delete();
    }

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

        $category->delete();
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
    public function it_returns_the_latest_posts()
    {
        $software = create(GroupCategory::class);
        $macos = create(Category::class, ['group_category_id' => $software->id]);
        $user = create(User::class);
        Carbon::setTestNow(Carbon::now()->subWeek());
        ThreadFactory::inCategory($macos)->by($user)->createMany(5);
        Carbon::setTestNow();
        $latestPosts = ThreadFactory::inCategory($macos)->by($user)->createMany(10);

        $response = $this->get(route('forum'));
        $latestPostIds = $response['latestPosts']->pluck('id');
        $this->assertTrue(
            $latestPosts->every(function ($latestPost) use ($latestPostIds) {
                return $latestPostIds->contains($latestPost->id);
            })
        );

        $software->delete();
        $user->delete();
    }

    /** @test */
    public function it_does_not_return_a_latest_post_if_it_is_ignored_by_the_authenticated_user()
    {
        $software = create(GroupCategory::class);
        $macos = create(Category::class, ['group_category_id' => $software->id]);
        $user = create(User::class);
        Carbon::setTestNow(Carbon::now()->subWeek());
        ThreadFactory::inCategory($macos)->by($user)->createMany(5);
        Carbon::setTestNow();
        $latestPosts = ThreadFactory::inCategory($macos)->by($user)->createMany(10);
        $this->signIn($user);
        $ignoredLatestPost = $latestPosts->first();
        $user->ignore($ignoredLatestPost);

        $response = $this->get(route('forum'));

        $unignoredLatestPosts = $latestPosts->where('id', '!=', $ignoredLatestPost->id);
        $latestPostIds = $response['latestPosts']->pluck('id');
        $this->assertTrue(
            $unignoredLatestPosts->every(function ($latestPost) use ($latestPostIds) {
                return $latestPostIds->contains($latestPost->id);
            })
        );
        $this->assertFalse($latestPostIds->contains($ignoredLatestPost->id));

        $software->delete();
        $user->delete();
    }

    /** @test */
    public function it_does_not_return_a_latest_post_if_the_creator_of_the_post_is_ignored_by_the_authenticated_user()
    {
        $software = create(GroupCategory::class);
        $macos = create(Category::class, ['group_category_id' => $software->id]);
        $john = create(User::class);
        $doe = create(User::class);
        Carbon::setTestNow(Carbon::now()->subWeek());
        ThreadFactory::inCategory($macos)->by($john)->createMany(5);
        Carbon::setTestNow();
        $latestPosts = ThreadFactory::inCategory($macos)->by($john)->createMany(9);
        ThreadFactory::inCategory($macos)->by($doe)->createMany(1);
        $this->signIn($john);
        $john->ignore($doe);
        $ignoredLatestPost = $doe->threads()->first();

        $response = $this->get(route('forum'));

        $unignoredLatestPosts = $latestPosts->where('id', '!=', $ignoredLatestPost->id);
        $latestPostIds = $response['latestPosts']->pluck('id');
        $this->assertTrue(
            $unignoredLatestPosts->every(function ($latestPost) use ($latestPostIds) {
                return $latestPostIds->contains($latestPost->id);
            })
        );
        $this->assertFalse($latestPostIds->contains($ignoredLatestPost->id));

        $software->delete();
        $john->delete();
        $doe->delete();
    }

}