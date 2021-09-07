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
     * @param string $searchQuery
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function search($searchQuery)
    {
        $query = '*' . $searchQuery . '*';

        return Thread::boolSearch()
            ->join(ProfilePost::class)
            ->join(Reply::class)
            ->should('wildcard', ['title' => $query])
            ->should('wildcard', ['body' => $query]);
    }
}