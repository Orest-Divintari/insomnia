<?php

namespace App\Filters;

use App\Filters\FilterInterface;
use App\Filters\Filters;
use App\Models\Thread;

class ElasticThreadFilters extends ElasticPostFilters implements FilterInterface
{
    /**
     * Supported filters for threads
     *
     * @var string[]
     */
    public $filters = [
        'postedBy',
        'lastCreated',
        'lastUpdated',
    ];

}