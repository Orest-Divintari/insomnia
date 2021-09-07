<?php

namespace App\Search;

use App\Models\User;

class SearchNames
{
    /**
     * Search all posts (thread, profile posts, replies)
     *
     * @param string|string[] $searchQuery
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function handle($searchQuery)
    {
        $users = User::boolSearch()
            ->should('match_phrase_prefix', ['name' => $searchQuery])
            ->size(10)
            ->execute()
            ->documents();

        $names = [];

        foreach ($users as $user) {
            $names[] = $user->getContent()['name'];
        }

        return $names;
    }
}