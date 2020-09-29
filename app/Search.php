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

        if (!isset($results) || empty($results)) {
            return 'No results';
        }

        return $this->getPaginatedData($results);
    }

    /**
     * Paginates and transforms the data if results are returned from activities table
     *
     * @param Collection $results
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedData($results)
    {
        $results = $results->paginate(static::RESULTS_PER_PAGE);
        if (array_key_exists('subject', $results->toArray()['data'][0])) {
            return $this->getActivitySubject($results);
        }
        return $results;
    }

    /**
     * When the activities table is used to get the results
     * then the results are enclosed in the subject attribute
     * This happens when there is no search query word
     *
     * @param Illuminate\Pagination\LengthAwarePaginator $results
     * @return Illuminate\Pagination\LengthAwarePaginator
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