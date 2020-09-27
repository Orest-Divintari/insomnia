<?php

namespace App\Search;

use App\Activity;
use App\Search\Threads;
use App\Thread;

class SearchThreads
{

    protected $filters;

    /**
     * Search threads and replies
     *
     * @return mixed
     */
    public function query()
    {
        $filters = app('ThreadFilters');
        $searchQuery = request('q');

        if (isset($searchQuery)) {
            $query = Threads::search($searchQuery);
        } else {
            $query = Activity::ofThreadsAndReplies();
        }
        return $filters->apply($query);
    }
}