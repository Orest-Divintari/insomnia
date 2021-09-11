<?php
namespace App\Search;

use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchThreadTitles implements SearchIndexInterface
{
    /**
     * Search threads titles
     *
     * @param string $query
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($query)
    {
        return Thread::boolSearch()
            ->should('query_string', [
                'default_field' => 'title',
                'query' => $query,
            ]);
    }
}