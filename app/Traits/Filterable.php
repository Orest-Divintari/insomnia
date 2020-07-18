<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{

    /**
     * Apply the given filters
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  \App\Filters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, $filters)
    {
        return $filters->apply($query);
    }
}