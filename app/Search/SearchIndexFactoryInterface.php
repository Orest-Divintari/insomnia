<?php

namespace App\Search;

use App\Search\SearchIndexInterface;

interface SearchIndexFactoryInterface
{
    /**
     * Create a new SeachIndexInterface object
     *
     * @param string $searchQuery
     * @param string $type
     * @param bool $onlyTitle
     * @return SearchIndexInterface
     */
    public function create($searchQuery, $type, $onlyTitle);
}