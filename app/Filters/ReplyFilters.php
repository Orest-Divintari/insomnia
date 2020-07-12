<?php

namespace App\Filters;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

class ReplyFilters extends Filters
{
    /**
     * Supported filters for replies
     *
     * @var array
     */
    protected $filters = ['sortByLikes'];

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