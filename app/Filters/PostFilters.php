<?php

namespace App\Filters;

use App\User;
use Carbon\Carbon;

class PostFilters
{

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return Builder
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
     * @return Builder
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

}