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
     * @param string $query
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($query)
    {
        return Thread::boolSearch()
            ->join(Reply::class)
            ->should('query_string', [
                'fields' => ['title', 'body'],
                'query' => $query,
            ])->minimumShouldMatch(1);
    }
}