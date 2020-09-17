<?php

namespace App\Filters;

use App\Filters\Filters;
use App\Filters\PostsFilter;
use App\Thread;
use App\User;
use Carbon\Carbon;

class ThreadFilters extends PostsFilter
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    protected $filters = [
        'postedBy',
        'newThreads',
        'newPosts',
        'contributed',
        'trending',
        'unanswered',
        'watched',
        'lastUpdated',
        'lastCreated',
        'numberOfReplies',
    ];

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
    public function contributed($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        $this->builder->whereHas('replies', function ($query) use ($user) {
            $query->where([
                ['position', ">", 1],
                'user_id' => $user->id,
            ]);
        });
    }

    /**
     * Get the Trending threads
     *
     * The trending thread is defined by the number of replies and views
     *
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function unanswered()
    {
        $this->builder->where('replies_count', '=', '0');
    }

    /**
     * Get the threads that the authenticated user has subscribed to
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function watched()
    {
        $this->builder->whereHas('subscriptions', function ($query) {
            $query->where('user_id', auth()->id());
        });

    }

    /**
     * Get the threads that were last updated before the given number of days
     *
     * @param int $numberOfDays
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function lastUpdated($numberOfDays)
    {
        $this->builder
            ->where('updated_at', ">=", Carbon::now()->subDays($numberOfDays));
    }

    /**
     * Get the threads with minimum number of rerplies
     *
     * @param int $numberOfReplies
     * @return Builder
     */
    public function numberOfReplies($numberOfReplies)
    {
        $this->builder->where('replies_count', '>=', $numberOfReplies);
    }

    /**
     * Get the filter keys and values passed in the request
     *
     * @return array
     */
    public function getThreadFilters()
    {
        return (new ManageThreadFilters($this->filters))
            ->getThreadFilters();
    }

}