<?php

namespace App\Traits;

trait Filterable
{

    /**
     * Apply the given filters
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  \App\Filters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}