<?php
namespace App\Search;

use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchTags implements SearchIndexInterface
{
    /**
     * Search all posts (thread, profile posts, replies)
     *
     * @param string|string[] $searchQuery
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($searchQuery)
    {
        $threadsBoolSearch = Thread::boolSearch();

        foreach ($searchQuery as $tag) {
            $threadsBoolSearch->filter('term', ['tagNames' => $tag]);
        }

        return $threadsBoolSearch->minimumShouldMatch(1);
    }
}