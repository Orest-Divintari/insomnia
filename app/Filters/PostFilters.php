<?php

namespace App\Filters;

use App\Actions\StringToArrayAction;
use App\Models\User;
use Carbon\Carbon;

class PostFilters
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
        $this->builder->whereIn('user_id', $userIds);
    }

    /**
     * Get the threads that were created the last given number of days
     *
     * @param int $daysAgo
     * @return void
     */
    public function lastCreated($daysAgo)
    {
        $daysAgo = Carbon::now()->subDays($daysAgo)->startOfDay();
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