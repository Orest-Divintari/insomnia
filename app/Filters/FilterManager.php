<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterManager
{

    public $request;
    public $builder;
    public $appliedFilters = [];
    public $supportedFilters = [];
    public $modelFilters = [];
    public $requestedFilters = [];

    /**
     * Create new FilterManager instance
     *
     * @param Request $request
     * @param array
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply filters on the given builder
     *
     * @param Builder|Builder[] $builder
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        if (gettype($builder) == 'array') {
            return $this->applyOnQueries($builder);
        }
        return $this->applyOnQuery($builder);
    }

    /**
     * Apply the filters on the given builder
     *
     * @param Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder $builder
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function applyOnQuery($builder)
    {
        foreach ($this->modelFilters as $modelFilterClass) {
            $modelFilter = app($modelFilterClass, compact('builder'));
            foreach ($this->getRequestedFilters($modelFilter) as $filter => $value) {
                if (method_exists($modelFilter, $filter)
                    && $this->canBeApplied($modelFilterClass, $filter)
                ) {
                    $modelFilter->$filter($value);

                    $this->appliedFilters[$modelFilterClass] = $filter;
                }
            }
            $builder = $modelFilter->builder();
        }
        return $builder;
    }

    /**
     * Apply the filters on multiple queries and finally union the filtered queries
     *
     * @param Builder[] $builders
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function applyOnQueries($builders)
    {
        $filteredQueries = [];
        foreach ($builders as $builder) {
            $filteredQueries[] = $this->applyOnQuery($builder);
        }
        $unionQuery = $filteredQueries[0];
        for ($queryCounter = 1; $queryCounter < count($builders); $queryCounter++) {
            $unionQuery = $unionQuery->union($filteredQueries[$queryCounter]);
        }
        return $unionQuery;
    }

    /**
     * Prevents the case where different modelFilters want to apply the same filter
     *
     * @param mixed $filter
     * @return boolean
     */
    public function canBeApplied($modelFilterClass, $filter)
    {
        if ($this->filterIsApplied($filter)) {
            return $this->filterClassIsApplied($modelFilterClass)
            && $this->filterIsAppliedByFilterClass($modelFilterClass, $filter);
        }
        return true;
    }

    /**
     * Chains model filters
     *
     * @param mixed $modelFilterClass
     * @param string $filter
     * @return bool
     */
    public function filterIsAppliedByFilterClass($modelFilterClass, $filter)
    {
        return collect($this->appliedFilters[$modelFilterClass])->contains($filter);
    }

    /**
     * Determine whether the given filter class has applied any filters
     *
     * @param mixed $modelFilterClass
     * @return bool
     */
    public function filterClassIsApplied($modelFilterClass)
    {
        return array_key_exists($modelFilterClass, $this->appliedFilters);
    }

    /**
     * Determine whether the given filter has been applied
     *
     * @param string $filter
     * @return bool
     */
    public function filterIsApplied($filter)
    {
        return collect($this->appliedFilters)
            ->flatten()
            ->contains($filter);
    }

    /**
     * Find and get the filters passed in the request
     *
     * @param mixed $modelFilter
     * @return array
     */
    public function getRequestedFilters($modelFilter = null)
    {
        if (isset($modelFilter)) {
            $this->requestedFilters = $this->getFiltersFor($modelFilter);
        } else {
            foreach ($this->supportedFilters as $modelFilter) {
                $this->requestedFilters[] = $this->getFiltersFor($modelFilter);
            }
        }
        $this->cleanUp();
        return $this->requestedFilters;
    }

    /**
     * Get the filters that are supported by the given model filter
     *
     * @param $modelFilter
     * @return array
     */
    public function getFiltersFor($modelFilter)
    {
        return request()->only($modelFilter->filters);
    }

    /**
     * Clean up the requested filters
     *
     * @param array $filters
     * @return void
     */
    public function cleanUp()
    {
        $this->discardEmpty();
        $this->castValues();
    }

    /**
     * Discards the requested filters that have no value
     *
     * @param array $fiters
     * @return array
     */
    public function discardEmpty()
    {
        $this->requestedFilters = array_filter(
            $this->requestedFilters,
            fn($value, $request) => isset($value),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Cast the requested filter values to boolean
     *
     * @param  array $filters
     * @return array
     */
    public function castValues()
    {
        $this->requestedFilters = collect($this->requestedFilters)
            ->map(function ($value, $key) {
                if ($value == 'true') {
                    $value = true;
                } elseif ($value == 'false') {
                    $value = false;
                }
                return $value;
            })->toArray();
    }

}