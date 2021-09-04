<?php
namespace App\Search;

use App\Models\Reply;
use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchThreads implements SearchIndexInterface
{

    /**
     * Search threads and replies for the given search query
     *
     * @param mixed $searchQuery
     * @return Laravel\Scout\Builder
     */
    public function search($searchQuery)
    {
        $query = '*' . $searchQuery . '*';

        return Thread::boolSearch()
            ->join(Reply::class)
            ->should('wildcard', ['title' => $query])
            ->should('wildcard', ['body' => $query]);

    }
}