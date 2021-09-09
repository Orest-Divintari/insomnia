<?php

namespace App\Filters;

use App\Actions\StringToArrayAction;
use App\Models\User;
use Carbon\Carbon;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;

class ElasticPostFilters implements FilterInterface
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
     * @var Laravel\Scout\Builder
     */
    protected $builder;

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return void
     */
    public function postedBy($usernames)
    {
        if (is_string($usernames)) {
            $usernames = (new StringToArrayAction($usernames))->execute();
        }
        $userIds = User::whereIn('name', $usernames)->pluck('id')->toArray();
        $this->builder->filter('terms', ['user_id' => $userIds]);
    }

    /**
     * Get the threads that are updated after the given date
     *
     * @param int $daysAgo
     * @return void
     */
    public function lastUpdated($daysAgo)
    {
        $daysAgo = Carbon::now()->subDays($daysAgo)->startOfDay();
        $this->builder->filter(
            (new RangeQueryBuilder())
                ->field('updated_at')
                ->gte($daysAgo)
        );
    }

    /**
     * Get the threads that are created after the given date
     *
     * @param int $daysAgo
     * @return void
     */
    public function lastCreated($daysAgo)
    {
        $daysAgo = Carbon::now()->subDays($daysAgo)->startOfDay();
        $this->builder->filter(
            (new RangeQueryBuilder())
                ->field('created_at')
                ->gte($daysAgo)
        );
    }

    /**
     * Return the builder
     *
     * @return Laravel\Scout\Builder|Illuminate\Database\Eloquent\Builder
     */
    public function builder()
    {
        return $this->builder;
    }

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

}