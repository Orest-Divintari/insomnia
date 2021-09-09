<?php

namespace App\Search;

use App\Search\SearchIndexFactoryInterface;
use App\Search\SearchIndexInterface;

class ElasticSearchIndexFactory implements SearchIndexFactoryInterface
{
    /**
     * Create the requested search index instance
     *
     * @param mixed $searchQuery
     * @param string $type
     * @param bool $onlyTitle
     * @return SearchIndexInterface
     */
    public function create($searchQuery, $type, $onlyTitle)
    {
        if ($type == 'thread' && !$onlyTitle) {
            return new ElasticSearchThreads();
        } elseif ($type == 'profile_post' && !$onlyTitle) {
            return new ElasticSearchProfilePosts();
        } elseif (empty($type) && !$onlyTitle) {
            return new ElasticSearchAllPosts();
        } elseif (!empty($searchQuery) && $onlyTitle) {
            return new ElasticSearchThreadTitles();
        } elseif ($type == 'tag') {
            return new ElasticSearchTags();
        }
    }

}