<?php

use App\Filters\Filters;
use App\User;

class ProfilePostFilters extends Filters
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    protected $filters = [
        'postedBy',
        'profileOwner',
        'lastCreated',
    ];

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function postedBy($username)
    {
        $user = User::whereName($username)->firstOrFail();

        $this->builder->where('user_id', $user->id);

    }
}
