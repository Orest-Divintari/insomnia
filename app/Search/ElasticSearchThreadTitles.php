<?php
namespace App\Search;

use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchThreadTitles implements SearchIndexInterface
{
    /**
     * Search threads titles
     *
     * @param string $searchQuery
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($searchQuery)
    {
        $query = '*' . $searchQuery . '*';

        return Thread::boolSearch()
            ->should('wildcard', ['title' => $query]);
    }
}