<?php

namespace App;

use App\Search\SearchAllPosts;
use App\Search\SearchProfilePosts;
use App\Search\SearchThreads;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

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

        return $this->getActivitySubject(
            $results->paginate(static::RESULTS_PER_PAGE)
        );
    }

    /**
     * When the activities table is used to get the results
     * then the results are enclosed in the subject attribute
     * This happens when there is no search query word
     *
     * @param Collection $results
     * @return void
     */
    public function getActivitySubject($results)
    {
        $data = collect(collect($results)['data']);
        if ($data->flatten()->contains('subject')) {
            $pagination = $results->toArray();
            $subjects = $data->pluck('subject');
            $results = new LengthAwarePaginator(
                $subjects,
                $pagination['total'],
                $pagination['per_page'],
                $pagination['current_page'],
                ['path' => $pagination['path']]
            );
        }
        return $results;
    }
}