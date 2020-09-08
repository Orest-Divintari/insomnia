<?php

namespace App\Http\Controllers\Api;

use App\Activity;
use App\Http\Controllers\Controller;
use App\User;

class LatestActivityController extends Controller
{
    /**
     * Get the activities of the user
     *
     * @param User $user
     * @param Bool $postings
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(User $user, $postings = false)
    {
        $activities = Activity::feed($user);

        if ($postings) {
            $activities->onlyPostings();
        }

        return $activities
            ->paginate(Activity::NUMBER_OF_ACTIVITIES);
    }
}