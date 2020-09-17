<?php

namespace App;

use Exception;

class Search
{
    /**
     * Number of results per page
     * @var int
     */
    const RESULTS_PER_PAGE = 10;

    public function getResults()
    {
        try
        {
            if (request('type') == 'thread') {
                $results = app(SearchThreads::class)->query();
            } elseif (request('type') == 'profile_post') {
                $results = app(SearchProfilePosts::class)->query();
            } elseif (request()->missing('type')) {
                $results = app(SearchAllPosts::class)->query();
            }
        } catch (Exception $e) {

            return 'No results';
        }

        if (is_null($results)) {
            return 'No results';
        }

        return $results->paginate(static::RESULTS_PER_PAGE);

    }
}