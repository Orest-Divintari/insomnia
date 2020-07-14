<?php

namespace App\Filters;

use App\Filters\Filters;
use App\Thread;
use App\User;

class ThreadFilters extends Filters
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    protected $filters = [
        'by',
        'newThreads',
        'newPosts',
        'participatedBy',
        'trending',
    ];

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function by($username)
    {
        $user = User::whereName($username)->firstOrFail();

        $this->builder->where('user_id', $user->id);

    }

    /**
     * Fetch the most recent threads that don't have replies
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newThreads()
    {

        $this->builder->orderBy('created_at', 'DESC');

    }

    /**
     * Fetch the most recent threads that have replies
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newPosts()
    {
        $this->builder->where('replies_count', '>', 0)
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * Fetch the threads that you have participated
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function participatedBy($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        $this->builder->whereHas("replies", function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

    }

    /**
     * Trending threads
     *
     * @return void
     */
    public function trending()
    {
        $this->builder->where('replies_count', '>', 0)
            ->orderBy('replies_count', 'DESC')
            ->orderBy('views', 'DESC');
    }

}