<?php

namespace App\Filters;

use App\User;
use Carbon\Carbon;

class PostsFilter extends Filters
{

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function postedBy($username)
    {
        $userId = User::whereName($username)->firstOrFail()->id;

        $this->builder->where('user_id', $userId);

    }

    /**
     * Get the threads that were created the last give number of days
     *
     * @param int $numberOfDays
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function lastCreated($numberOfDays)
    {
        $this->builder
            ->where(
                'created_at', ">=",
                Carbon::now()->subDays($numberOfDays)
            );
    }

}