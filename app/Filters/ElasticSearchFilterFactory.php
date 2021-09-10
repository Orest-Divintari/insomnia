<?php

namespace App\Filters;

use App\Filters\FilterManager;
use App\Filters\SearchFilterFactoryInterface;

class ElasticSearchFilterFactory implements SearchFilterFactoryInterface
{

    protected $filterManager;

    /**r
     * Create a new instance
     *
     * @param FilterManager $filterManager
     */
    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * Create a filter manager with the requested model filters
     *
     * @param string $type
     * @return FilterManager
     */
    public function create(string $type)
    {
        return match($type) {
            'thread' => $this->filterManager->withElasticThreadFilters(),
            'tag' => $this->filterManager->withElasticThreadFilters(),
            'profile_post' => $this->filterManager->withElasticProfilePostFilters(),
        default=> $this->filterManager->withElasticAllPostsFilters()
        };
    }
}