<?php

namespace App\Search;

use App\Search\SearchIndexInterface;

interface SearchIndexFactoryInterface
{
    /**
     * Create a new SeachIndexInterface object
     *
     * @param mixed $searchQuery
     * @param string $type
     * @param bool $onlyTitle
     * @return SearchIndexInterface
     */
    public function create($searchQuery, $type, $onlyTitle);
}