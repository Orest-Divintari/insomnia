<?php

namespace App\ViewModels;

use App\Thread;
use App\User;

class LatestPostsViewModel
{
    public function recentlyActiveThreads($excludeIgnored)
    {
        $query = Thread::query();

        if (auth()->check()) {

            $authUser = auth()->user();

            $query->excludeIgnored($authUser, $excludeIgnored)
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
}