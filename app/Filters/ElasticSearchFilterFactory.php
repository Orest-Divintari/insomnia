<?php

namespace App\Filters;

use App\Filters\FilterManager;
use App\Filters\SearchFilterFactoryInterface;

class ElasticSearchFilterFactory implements SearchFilterFactoryInterface
{

    protected $filterManager;

    /**
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
        if ($type == 'thread' || $type == 'tag') {
            return $this->filterManager->withElasticThreadFilters();
        } elseif ($type == 'profile_post') {
            return $this->filterManager->withElasticProfilePostFilters();
        } elseif (empty($type)) {
            return $this->filterManager->withElasticAllPostsFilters();
        }
    }
}