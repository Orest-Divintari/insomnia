<?php

namespace App\Filters;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

class ReplyFilters
{
    /**
     * Supported filters for replies
     *
     * @var array
     */
    public $filters = ['sortByLikes'];

    public $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Order replies by the number of likes
     *
     * @return Illuminate\Database\Eloquent\Builder $builder
     */
    public function sortByLikes()
    {
        $this->builder->orderBy('likes_count', 'DESC');
    }
}