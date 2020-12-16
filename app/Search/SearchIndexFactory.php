<?php

namespace App\Search;

use App\Search\SearchAllPosts;
use App\Search\SearchIndexFactoryInterface;
use App\Search\SearchIndexInterface;
use App\Search\SearchProfilePosts;
use App\Search\SearchThreads;
use App\Search\SearchThreadsTitle;

class SearchIndexFactory implements SearchIndexFactoryInterface
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
            return new SearchThreads();
        } elseif ($type == 'profile_post' && !$onlyTitle) {
            return new SearchProfilePosts();
        } elseif (empty($type) && !$onlyTitle) {
            return new SearchAllPosts();
        } elseif (!empty($searchQuery) && $onlyTitle) {
            return new SearchThreadsTitle();
        } elseif ($type == 'tag') {
            return new SearchTags();
        }
    }

}