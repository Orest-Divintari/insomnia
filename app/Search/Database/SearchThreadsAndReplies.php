<?php

namespace App\Search\Database;

use App\Activity;
use App\Search\SearchStrategyInterface;
use Illuminate\Http\Request;

class SearchThreadsAndReplies implements SearchStrategyInterface
{

    /**
     * Get the activity of threads and replies
     *
     * @param Request $request
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request)
    {
        return Activity::ofThreadsAndReplies();
    }
}