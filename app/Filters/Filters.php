<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Filters
{

    protected $request;
    protected $builder;
    protected $filters = [];

    /**
     * Create new Filters instance
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply filters
     *
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter) {

            if (method_exists($this, $filter)) {
                $this->$filter($this->request->$filter);
            }
        }
        return $this->builder;

    }

    /**
     * Find and get the filters passed in the request
     *
     * @return array
     */
    public function getFilters()
    {
        return array_intersect(array_keys($this->request->all()), $this->filters);
    }

}