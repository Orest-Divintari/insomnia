<?php

namespace App\Search\Database;

use App\Search\Database\SearchAllPosts;
use App\Search\Database\SearchProfilePosts;
use App\Search\Database\SearchThreadsAndReplies;
use App\Search\SearchStrategyFactoryInterface;
use Illuminate\Http\Request;

class DatabaseSearchStrategyFactory implements SearchStrategyFactoryInterface
{

    /**
     * Create a DatabaseSearchStrategy instance to search the database
     *
     * @param Request $request
     * @return SearchStrategyInterface
     */
    public function create(Request $request)
    {
        $type = $request->input('type');

        if ($type == 'thread') {
            return new SearchThreadsAndReplies();
        } elseif ($type == 'profile_post') {
            return new SearchProfilePosts();
        } elseif (is_null($type)) {
            return new SearchAllPosts();
        }
    }
}