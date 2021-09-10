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
    protected $filters = [];
    protected $requestedFilters = [];
    protected $chain;

    /**
     * Create a new FilterManager instance
     *
     * @param FilterChain $chain
     */
    public function __construct(FilterChain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * Apply filters on the given builder
     *
     * @param Builder
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        foreach ($this->chain->getFilters() as $filterClass) {
            $filter = new $filterClass();
            $filter->setBuilder($builder);

            foreach ($this->getRequestedFilters($filter) as $filterKey => $filterValue) {

                if (method_exists($filter, $filterKey)
                    && $this->canBeApplied($filterClass, $filterKey)
                ) {
                    $filter->$filterKey($filterValue);
                    $this->appliedFilters[$filterClass][] = $filterKey;
                }
            }
            $builder = $filter->builder();
        }
        return $builder;
    }

    /**
     * Prevents the case where different filters want to apply the same filter
     *
     * @param string $filterClass
     * @param string $filterKey
     * @return boolean
     */
    private function canBeApplied($filterClass, $filterKey)
    {
        if ($this->filterIsApplied($filterKey)) {
            return $this->filterClassIsApplied($filterClass)
            && $this->filterIsAppliedByFilterClass($filterClass, $filterKey);
        }
        return true;
    }

    /**
     * Determine whether the given filterKey has been applied by the given filter
     *
     * @param string $filterClass
     * @param string $filterKey
     * @return bool
     */
    private function filterIsAppliedByFilterClass($filterClass, $filterKey)
    {
        return collect($this->appliedFilters[$filterClass])
            ->contains($filterKey);
    }

    /**
     * Determine whether the given filter class has applied any filters
     *
     * @param string $filterClass
     * @return bool
     */
    private function filterClassIsApplied($filterClass)
    {
        return array_key_exists($filterClass, $this->appliedFilters);
    }

    /**
     * Determine whether the given filter has been applied
     *
     * @param string $filter
     * @return bool
     */
    private function filterIsApplied($filterKey)
    {
        return collect($this->appliedFilters)
            ->flatten()
            ->contains($filterKey);
    }

    /**
     * Find and get the filters passed in the request
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getRequestedFilters($filter = null)
    {
        if (isset($filter)) {
            $this->requestedFilters = $this->getFiltersFor($filter);
        } else {
            foreach ($this->chain->getFilters() as $filterClass) {
                $this->requestedFilters = $this->getFiltersFor(new $filterClass());
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
     * @param FilterInterface $filter
     * @return array
     */
    private function getFiltersFor($filter)
    {
        $filters = $this->toSnakeCase($filter->filters);

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

    /**
     * Add Elastic thread filters to the chain
     *
     * @return FilterManager
     */
    public function withElasticThreadFilters()
    {
        $this->chain->withElasticThreadFilters();
        return $this;
    }

    /**
     * Add Elastic thread filters to the chain
     *
     * @return FilterManager
     */
    public function withElasticProfilePostFilters()
    {
        $this->chain->withElasticProfilePostFilters();
        return $this;
    }

    /**
     * Add Elastic all posts filters to the chain
     *
     * @return FilterManager
     */
    public function withElasticAllPostsFilters()
    {
        $this->chain->withElasticAllPostsFilters();
        return $this;
    }

    /**
     * Get all managed filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->chain->getFilters();
    }

}