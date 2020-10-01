<?php

namespace App\Filters;

use App\User;
use Carbon\Carbon;

class Filters
{

    /**
     * The supported filters to operate upon
     *
     * @var array
     */
    public $filters = [];

    /**
     * Builder on which the filters are applied
     *
     * @var Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Create a new ThreadFilters instance
     *
     * @param Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder $builder
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return void
     */
    public function postedBy($username)
    {
        $userId = User::whereName($username)->firstOrFail()->id;
        $this->builder->where('user_id', $userId);
    }

    /**
     * Get the threads that were created the last given number of days
     *
     * @param int $daysAgo
     * @return void
     */
    public function lastCreated($daysAgo)
    {
        $daysAgo = Carbon::now()->subDays($daysAgo);

        if (is_subclass_of($this->builder, 'Laravel\Scout\Builder')) {
            $this->builder
                ->where('created_at', '>=', $daysAgo->timestamp);
        } else {
            $this->builder
                ->where('created_at', ">=", $daysAgo);
        }
    }

    /**
     * Return the builder
     *
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}