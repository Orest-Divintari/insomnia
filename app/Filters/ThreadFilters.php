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
        'unanswered',
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
     * Fetch the most recently created threads
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newThreads()
    {

        $this->builder->orderBy('created_at', 'DESC');

    }

    /**
     * Fetch the threads with the most recent replies
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
     * Get the Trending threads
     *
     * The trending thread is defined by the number of replies and views
     *
     * @return void
     */
    public function trending()
    {
        $this->builder->where('replies_count', '>', 0)
            ->orderBy('replies_count', 'DESC')
            ->orderBy('views', 'DESC');
    }

    /**
     * Get the threads that have no replies
     *
     * @return void
     */
    public function unanswered()
    {
        $this->builder->where('replies_count', '=', '0');
    }

}