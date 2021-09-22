<?php

namespace App\Search;

use App\Models\User;

class SearchNames
{
    /**
     * Search all posts (thread, profile posts, replies)
     *
     * @param string|string[] $query
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    public function handle($query)
    {
        $users = User::boolSearch()
            ->must('match_phrase_prefix', ['name' => $query])
            ->must('exists', ['field' => 'email_verified_at'])
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