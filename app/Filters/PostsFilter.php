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
        $user = User::whereName($username)->firstOrFail();

        $this->builder->where('user_id', $user->id);

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