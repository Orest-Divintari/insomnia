<?php

namespace App\Filters;

interface SearchFilterFactory
{
    public function __construct(FilterManager $filterManager);
    public function create(string $type);
}