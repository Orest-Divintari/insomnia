<?php
namespace App\Search;

use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchThreadTitles implements SearchIndexInterface
{

    /**
     * Search threads titles
     *
     * @param mixed $searchQuery
     * @return Laravel\Scout\Builder
     */
    public function search($searchQuery)
    {
        $query = '*' . $searchQuery . '*';

        return Thread::boolSearch()
            ->should('wildcard', ['title' => $query]);
    }
}