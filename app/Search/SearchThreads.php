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
            $query = Activity::whereHasMorph('subject', ['App\Reply'], function ($builder) {
                $builder->where('repliable_type', 'App\Thread');
            })->orWhere('subject_type', 'App\Thread');
        }
        return $filters->apply($query);
    }
}