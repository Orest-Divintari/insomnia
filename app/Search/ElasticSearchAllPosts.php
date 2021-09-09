<?php
namespace App\Search;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Thread;
use App\Search\SearchIndexInterface;

class ElasticSearchAllPosts implements SearchIndexInterface
{
    /**
     * Search all posts (thread, profile posts, replies)
     *
     * @param string $query
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($query)
    {
        return Thread::boolSearch()
            ->join(ProfilePost::class)
            ->join(Reply::class)
            ->should('match_phrase', ['title' => $query])
            ->should('match_phrase', ['body' => $query]);
    }
}