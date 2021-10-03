<?php

namespace Database\Seeders;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use \Illuminate\Database\Eloquent\Collection;

trait RandomModels
{

    /**
     * Get a number of random users
     *
     * @param integer $numberOfUsers
     * @return Collection
     */
    protected function randomUsers($numberOfUsers = 1)
    {
        return User::inRandomOrder()
            ->limit($numberOfUsers)
            ->get();
    }

    /**
     * Get a random user
     *
     * @return User
     */
    protected function randomUser()
    {
        return User::inRandomOrder()
            ->limit(1)
            ->first();
    }

    /**
     * Get a number of random users except the given user(s)
     *
     * @param int $numberOfUsers
     * @param User|Collection $users
     * @return Collection
     */
    protected function randomUsersExcept($numberOfUsers, $users)
    {
        $userIds = match(true) {
            $users instanceof User => collect([$users->id]),
            $users instanceof Collection => $users->pluck('id')
        };

        return User::inRandomOrder()
            ->limit($numberOfUsers)
            ->whereNotIn('id', $userIds)
            ->get();
    }

    /**
     * Get a number of random threads
     *
     * @param int $numberOfThreads
     * @return Collection
     */
    protected function randomThreads($numberOfThreads)
    {
        return Thread::inRandomOrder()
            ->limit($numberOfThreads)
            ->get();
    }

    /**
     * Get a number of random profile posts
     *
     * @param int $numberOfProfilePosts
     * @return Collection
     */
    protected function randomProfilePosts($numberOfProfilePosts)
    {
        return ProfilePost::inRandomOrder()
            ->limit($numberOfProfilePosts)
            ->get();
    }

    /**
     * Get a number of random comments
     *
     * @param int $numberOfComments
     * @return Collection
     */
    protected function randomComments($numberOfComments)
    {
        return Reply::query()
            ->comment()
            ->inRandomOrder()
            ->limit($numberOfComments)
            ->get();
    }

    /**
     * Get a number of random thread replies
     *
     * @param int $numberOfThreadReplies
     * @return Collection
     */
    protected function randomThreadReplies($numberOfThreadReplies)
    {
        return Reply::query()
            ->thread()
            ->inRandomOrder()
            ->limit($numberOfThreadReplies)
            ->get();
    }

}