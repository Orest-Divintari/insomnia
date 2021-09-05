<?php

namespace App\Filters;

class SearchThreadFilters
{
    protected $filters = [
        'postedBy',
        'lastCreated',
    ];

    /**
     * Apply filters
     *
     * @param \Laravel\Scout\Builder $builder
     * @return \Laravel\Scout\Builder $builder
     */
    public function apply($builder)
    {
        
    }

    public function postedBy()
    {

    }

}