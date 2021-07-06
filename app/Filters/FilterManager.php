<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterManager
{
    protected $request;
    protected $builder;
    protected $appliedFilters = [];
    protected $supportedFilters = [];
    protected $modelFilters = [];
    protected $requestedFilters = [];
    protected $chain;

    /**
     * Create a new FilterManager instance
     *
     * @param ModelFilterChain $chain
     */
    public function __construct(ModelFilterChain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * Apply filters on the given builder
     *
     * @param Builder
     * @return Laravel\ScoutExtended\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        foreach ($this->chain->getFilters() as $modelFilterClass) {
            $modelFilter = new $modelFilterClass();
            $modelFilter->setBuilder($builder);

            foreach ($this->getRequestedFilters($modelFilter) as $filter => $value) {

                if (method_exists($modelFilter, $filter)
                    && $this->canBeApplied($modelFilterClass, $filter)
                ) {
                    $modelFilter->$filter($value);
                    $this->appliedFilters[$modelFilterClass][] = $filter;
                }
            }
            $builder = $modelFilter->builder();
        }
        return $builder;
    }

    /**
     * Prevents the case where different modelFilters want to apply the same filter
     *
     * @param mixed $filter
     * @return boolean
     */
    private function canBeApplied($modelFilterClass, $filter)
    {
        if ($this->filterIsApplied($filter)) {
            return $this->filterClassIsApplied($modelFilterClass)
            && $this->filterIsAppliedByFilterClass($modelFilterClass, $filter);
        }
        return true;
    }

    /**
     * Determine whether the given filter has been applied by the given model filter
     *
     * @param mixed $modelFilterClass
     * @param string $filter
     * @return bool
     */
    private function filterIsAppliedByFilterClass($modelFilterClass, $filter)
    {
        return collect($this->appliedFilters[$modelFilterClass])
            ->contains($filter);
    }

    /**
     * Determine whether the given filter class has applied any filters
     *
     * @param mixed $modelFilterClass
     * @return bool
     */
    private function filterClassIsApplied($modelFilterClass)
    {
        return array_key_exists($modelFilterClass, $this->appliedFilters);
    }

    /**
     * Determine whether the given filter has been applied
     *
     * @param string $filter
     * @return bool
     */
    private function filterIsApplied($filter)
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
            foreach ($this->chain->getFilters() as $modelFilterClass) {
                $this->requestedFilters = $this->getFiltersFor(new $modelFilterClass());
            }
        }

        $this->cleanUp();

        return $this->requestedFilters;
    }

    /**
     * Convert to filter keys to camel case
     *
     * @param array
     * @return array
     */
    private function toCamelCase($filters)
    {
        $camelCaseKeyFilters = [];
        foreach ($filters as $filterKey => $filterValue) {
            if (!str_contains($filterKey, '_')) {
                $camelCaseKeyFilters[$filterKey] = $filterValue;
                continue;
            }
            $splitKey = explode('_', $filterKey);
            $splitKey[1] = ucwords($splitKey[1]);
            $camelCaseKey = implode('', $splitKey);
            $camelCaseKeyFilters[$camelCaseKey] = $filterValue;
        }
        return $camelCaseKeyFilters;
    }

    /**
     * Get the filters that are supported by the given model filter
     *
     * @param $modelFilter
     * @return array
     */
    private function getFiltersFor($modelFilter)
    {
        $filters = $this->toSnakeCase($modelFilter->filters);

        return $this->toCamelCase(request()->only($filters));
    }

    /**
     * Convert to filter keys to snake case
     *
     * @param array $filters
     * @return array
     */
    private function toSnakeCase($filters)
    {
        $snakeCaseFilters = [];
        foreach ($filters as $filterKey) {
            $snakeCaseFilter = strtolower(
                preg_replace(
                    '/(?<!^)[A-Z]/',
                    '_$0',
                    $filterKey
                )
            );
            $snakeCaseFilters[] = $snakeCaseFilter;
        }
        return $snakeCaseFilters;
    }

    /**
     * Clean up the requested filters
     *
     * @param array $filters
     * @return void
     */
    private function cleanUp()
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
    private function discardEmpty()
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
    private function castValues()
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

    /**
     * Add ConversationFilters to the chain
     *
     * @return FilterManager
     */
    public function withConversationFilters()
    {
        $this->chain->withConversationFilters();
        return $this;
    }

    /**
     * Add ThreadFilters to the chain
     *
     * @return FilterManager
     */
    public function withThreadFilters()
    {
        $this->chain->withThreadFilters();
        return $this;
    }

    /**
     * Add ReplyFilters to the chain
     *
     * @return FilterManager
     */
    public function withReplyFilters()
    {
        $this->chain->withReplyFilters();
        return $this;
    }

    /**
     * Add ProfilePostFilters to the chain
     *
     * @return FilterManager
     */
    public function withProfilePostFilters()
    {
        $this->chain->withProfilePostFilters();
        return $this;
    }

    /**
     * Add all post filters to the chain
     *
     * @return FilterManager
     */
    public function withAllPostsFilters()
    {
        $this->chain->withAllPostsFilters();
        return $this;
    }

}