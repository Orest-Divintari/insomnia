<?php

namespace App\Search\Algolia;

use App\Search;
use App\Search\Algolia\SearchAllPosts;
use App\Search\Algolia\SearchProfilePosts;
use App\Search\Algolia\SearchThreads;
use App\Search\Algolia\SearchThreadsTitle;
use App\Search\SearchStrategyFactoryInterface;
use Illuminate\Http\Request;

class AlgoliaSearchStrategyFactory implements SearchStrategyFactoryInterface
{
    /**
     * Create a new Algolia Searchable instance
     *
     * @return Searchable
     */
    public function create(Request $request)
    {
        $type = $request->input('type');
        $onlyTitle = $request->input('only_title');

        if ($type == 'thread' && !$onlyTitle) {
            return new SearchThreads();
        } elseif ($type == 'profile_post' && !$onlyTitle) {
            return new SearchProfilePosts();
        } elseif (empty($type) && !$onlyTitle) {
            return new SearchAllPosts();
        } elseif ($onlyTitle) {
            return new SearchThreadsTitle();
        }
    }
}