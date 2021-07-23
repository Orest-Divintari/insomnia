<?php

namespace App\ViewModels;

use App\Filters\ExcludeIgnoredFilter;
use App\Models\GroupCategory;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;

class ForumViewModel
{
    public function groups()
    {
        return GroupCategory::withCategories()->get();
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

        return $query->with('category')
            ->withRecentReply()
            ->latest('updated_at')
            ->take(10)
            ->get();
    }

    public function statistics()
    {
        return [
            'threads_count' => Thread::count(),
            'replies_count' => Reply::count(),
            'users_count' => User::count(),
        ];
    }
}
