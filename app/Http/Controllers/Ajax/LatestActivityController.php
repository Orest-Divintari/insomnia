<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;

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
