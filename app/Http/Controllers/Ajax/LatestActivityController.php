<?php

namespace App\Http\Controllers\Ajax;

use App\Activity;
use App\Http\Controllers\Controller;
use App\User;

class LatestActivityController extends Controller
{
    /**
     * Get the activities of the user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(User $user)
    {
        return Activity::feed($user)
            ->paginate(Activity::NUMBER_OF_ACTIVITIES);
    }
}
