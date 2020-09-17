<?php

namespace App\Filters;

use App\Filters\ProfilePostFilters;
use App\Filters\ThreadFilters;

class AllPostsFilter
{

    protected $filters = [
        ThreadFilters::class,
        ProfilePostFilters::class,
    ];

    public function apply($builder)
    {
        foreach ($this->filters as $filter) {
            $builder = app($filter)->apply($builder);
        }
        return $builder;
    }
}