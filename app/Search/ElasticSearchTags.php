<?php
namespace App\Search;

use App\Models\Tag;
use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchTags implements SearchIndexInterface
{
    /**
     * Search all posts (thread, profile posts, replies)
     *
     * @param mixed $searchQuery
     * @return Laravel\Scout\Builder
     */
    public function search($searchQuery)
    {
        $query = '*' . $searchQuery . '*';

        return Tag::boolSearch()
            ->should('wildcard', ['name' => $query]);
    }
}