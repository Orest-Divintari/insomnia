<?php

namespace App\ViewModels;

use App\Facades\Statistics;
use App\Filters\ExcludeIgnoredFilter;
use App\Models\GroupCategory;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ForumViewModel
{

    /**
     * Create a new instance
     *
     * @param ExcludeIgnoredFilter $excludeIgnoredFilter
     */
    public function __construct(protected ExcludeIgnoredFilter $excludeIgnoredFilter)
    {

    }

    /**
     * The time period for which the feed will be stored in cache
     */
    const FEED_CACHE_TIMEFRAME = 60;

    /**
     * The time period for which the latest posts will be soted in cache
     */
    const LATEST_POSTS_CACHE_TIMEFRAME = 60;

    public function groups()
    {
        return Cache::remember('forum.feed', static::FEED_CACHE_TIMEFRAME, function () {
            return GroupCategory::withCategories()->get();
        });
    }

    public function latestPosts()
    {
        $query = Thread::query();

        if (auth()->check()) {

            $authUser = auth()->user();

            $query->excludeIgnored($authUser, $this->excludeIgnoredFilter)
                ->whereRaw('threads.id NOT IN
                        (
                                SELECT repliable_id
                                FROM   replies
                                WHERE  repliable_type=?
                                AND    replies.user_id IN
                                        (
                                            SELECT ignorable_id
                                            FROM   ignorations
                                            WHERE  ignorable_type=?
                                            AND    user_id=?
                                        )
                        )', [Thread::class, User::class, $authUser->id]
                );
        }

        return Cache::remember(
            'forum.latest-posts',
            static::LATEST_POSTS_CACHE_TIMEFRAME,
            function () use ($query) {
                return $query->with('category')
                    ->withRecentReply()
                    ->latest('updated_at')
                    ->take(10)
                    ->get();
            });
    }

    public function statistics()
    {
        return [
            'threads_count' => Statistics::threads()->count(),
            'thread_replies_count' => Statistics::threadReplies()->count(),
            'users_count' => Statistics::users()->count(),
        ];
    }

    public function resetCache()
    {
        Cache::forget('forum.feed');
        Cache::forget('forum.latest-posts');
    }
}