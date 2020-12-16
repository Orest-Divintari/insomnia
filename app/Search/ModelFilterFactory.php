<?php

namespace App\Search;

use App\Filters\FilterManager;
use DeepCopy\Filter\Filter;
use Illuminate\Http\Request;

class ModelFilterFactory
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
            return $this->filterManager->withThreadFilters();
        } elseif ($type == 'profile_post') {
            return $this->filterManager->withProfilePostFilters();
        } elseif (empty($type)) {
            return $this->filterManager->withAllPostsFilters();
        }
    }
}