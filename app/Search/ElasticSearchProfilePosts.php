<?php
namespace App\Search;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Search\SearchIndexInterface;

class ElasticSearchProfilePosts implements SearchIndexInterface
{

    /**
     * Search profile posts and comments for the given search query
     *
     * @param string $searchQuery
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($searchQuery)
    {
        $query = '*' . $searchQuery . '*';

        return ProfilePost::boolSearch()
            ->join(Reply::class)
            ->should('wildcard', ['title' => $query])
            ->should('wildcard', ['body' => $query]);

    }
}