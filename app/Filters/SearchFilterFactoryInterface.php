<?php

namespace App\Filters;

interface SearchFilterFactoryInterface
{
    public function __construct(FilterManager $filterManager);
    public function create(string $type);
}