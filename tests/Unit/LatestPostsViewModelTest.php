<?php

namespace Tests\Unit;

use App\Filters\ExcludeIgnoredFilter;
use App\Thread;
use App\User;
use App\ViewModels\LatestPostsViewModel;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LatestPostsViewModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_the_latest_posts_excluding_threads_that_are_ignored()
    {
        $threads = createMany(Thread::class, 5);
        $john = $this->signIn();
        $ignoredThread = $threads->first()->markAsIgnored($john);
        $excludeIgnored = app(ExcludeIgnoredFilter::class);

        $latestPosts = app(LatestPostsViewModel::class)->recentlyActiveThreads($excludeIgnored);

        $this->assertFalse($latestPosts->pluck('id')->contains($ignoredThread->id));
    }

    /** @test */
    public function it_returns_the_latest_posts_excluding_threads_that_are_created_by_users_that_are_ignored()
    {
        $threads = createMany(Thread::class, 4);
        $doe = create(User::class);
        $threadByDoe = ThreadFactory::by($doe)->create();
        $john = $this->signIn();
        $doe->markAsIgnored($john);
        $excludeIgnored = app(ExcludeIgnoredFilter::class);

        $latestPosts = app(LatestPostsViewModel::class)->recentlyActiveThreads($excludeIgnored);

        $this->assertFalse($latestPosts->pluck('id')->contains($threadByDoe->id));
    }

    /** @test */
    public function it_returns_the_latest_posts_excluding_the_threads_that_an_ignored_user_has_replied_to()
    {
        $threads = createMany(Thread::class, 4);
        $threadToBeIgnored = $threads->first();
        $doe = create(User::class);
        $replyByDoe = ReplyFactory::toThread($threadToBeIgnored)->by($doe)->create();
        $john = $this->signIn();
        $doe->markAsIgnored($john);
        $excludeIgnored = app(ExcludeIgnoredFilter::class);

        $latestPosts = app(LatestPostsViewModel::class)->recentlyActiveThreads($excludeIgnored);

        $this->assertFalse($latestPosts->pluck('id')->contains($threadToBeIgnored->id));
    }
}