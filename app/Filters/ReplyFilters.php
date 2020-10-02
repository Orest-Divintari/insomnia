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
     * Create a new ReplyFilters instance.
     *
     * @param Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder $builder
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
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

    /**
     * Returns the builder
     *
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function builder()
    {
        return $this->builder;
    }
}