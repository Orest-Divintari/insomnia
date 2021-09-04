<?php

namespace App\Search;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use Illuminate\Pagination\LengthAwarePaginator;

class ElasticSearch extends Search
{
    /**
     * Get the results from the database if there are any
     * otherwise return no results message
     *
     * @param Collection $results
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function fetch($results)
    {
        $results = $results->paginate(self::RESULTS_PER_PAGE);

        $paginatedResults = $this->paginate($results);

        $results = $this->appendHasIgnoredContentAttributeAction->execute($paginatedResults);

        if (empty($results['data'])) {
            return $this->noResults();
        }

        return $results;
    }

    /**
     * Undocumented function
     *
     * @param collection $results
     * @return LengthAwarePaginator
     */
    private function paginate($results)
    {
        return new LengthAwarePaginator(
            $results->models(),
            $results->total(),
            $results->perPage(),
            $results->currentPage(),
            ['path' => $results->path()]
        );
    }

    /**
     * Return message when no results are found
     *
     * @return string
     */
    public function noResults()
    {
        return 'No results found.';
    }
}