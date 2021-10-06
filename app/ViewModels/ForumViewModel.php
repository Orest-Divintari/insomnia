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
    public function groups()
    {
        return Cache::remember('forum.home', 60, function () {
            return GroupCategory::withCategories()->get();
        });
    }

    public function latestPosts(ExcludeIgnoredFilter $excludeIgnoredFilter)
    {
        $query = Thread::query();

        if (auth()->check()) {

            $authUser = auth()->user();

            $query->excludeIgnored($authUser, $excludeIgnoredFilter)
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

        return Cache::store('redis')->remember('forum.latest-posts', 60, function () use ($query) {
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
}