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

    /**
     * The builder on which the filters are applied
     *
     * @var Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public $builder;

    /**
     * Set the builder
     *
     * @param Builder $builder
     * @return void
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Return the builder
     *
     * @return Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * Order replies by the number of likes
     *
     * @return void
     */
    public function sortByLikes()
    {
        $this->builder->orderBy('likes_count', 'DESC');
    }

}