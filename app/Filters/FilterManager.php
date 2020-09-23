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
     * Apply filters
     *
     * @param mixed $builder
     * @return mixed
     */
    public function apply($builder)
    {
        foreach ($this->modelFilters as $modelFilterClass) {

            $modelFilter = app($modelFilterClass, compact('builder'));

            foreach ($this->getRequestedFilters($modelFilter) as $filter => $value) {
                if (method_exists($modelFilter, $filter) && $this->isNotApplied($filter)) {

                    $modelFilter->$filter($value);

                    $this->appliedFilters[] = $filter;
                }
            }
            $builder = $modelFilter->builder;
        }
        return $builder;
    }

    /**
     * Determine if the given filter has been already applied
     *
     * @param mixed $filter
     * @return boolean
     */
    public function isNotApplied($filter)
    {
        return !in_array($filter, $this->appliedFilters);
    }

    /**
     * Chains model filters
     *
     * @param  $modelFilter
     * @return void
     */
    public function addFilter($modelFilter)
    {
        $this->modelFilters[] = $modelFilter;
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
        $this->getNonEmpty();
        $this->castValues();
    }

    /**
     * Filters out the requested filters that have no value
     *
     * @param array $fiters
     * @return array
     */
    public function getNonEmpty()
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