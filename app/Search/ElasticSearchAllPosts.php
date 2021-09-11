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
        return Reply::boolSearch()
            ->join(Thread::class)
            ->join(ProfilePost::class)
            ->should('query_string', [
                'fields' => ['title', 'body'],
                'query' => $query,
            ]);

    }
}