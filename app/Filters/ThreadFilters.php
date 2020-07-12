<?php

namespace App\Filters;

use App\Filters\Filters;
use App\User;

class ThreadFilters extends Filters
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    protected $filters = ['by'];

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function by($username)
    {
        $user = User::whereName($username)->firstOrFail();

        $this->builder->where('user_id', $user->id);

    }

}