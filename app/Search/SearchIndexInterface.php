<?php

namespace App\Search;

interface SearchIndexInterface
{
    /**
     * Search the index with the given searchQuery
     *
     * @param string|string[] $searchqQuery
     * @return mixed
     */
    public function search($searchqQuery);
}