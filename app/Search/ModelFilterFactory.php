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
     * Create the requested model filter
     *
     * @param Request $request
     * @return FilterManager
     */
    public function create(Request $request)
    {
        $type = $request->input('type');

        if ($type == 'thread') {
            return $this->filterManager->withThreadFilters();
        } elseif ($type == 'profile_post') {
            return $this->filterManager->withProfilePostFilters();
        } elseif (is_null($type)) {
            return $this->filterManager->withAllPostsFilters();
        }
    }
}