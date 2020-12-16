<?php

namespace App\Search;

interface SearchIndexInterface
{
    /**
     * Search the index with the given searchQuery
     *
     * @param $searchqQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search($searchqQuery);
}