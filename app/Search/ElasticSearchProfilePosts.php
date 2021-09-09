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
     * @param string $query
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($query)
    {
        return ProfilePost::boolSearch()
            ->join(Reply::class)
            ->should('match_phrase', ['title' => $query])
            ->should('match_phrase', ['body' => $query]);

    }
}