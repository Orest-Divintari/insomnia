<?php

namespace App\Repositories;

use App\Activity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OnlineRepository
{
    const ACTIVITIES_PER_PAGE = 10;

    /**
     * Online registered users count
     *
     * @var int
     */
    private $membersCount;

    /**
     * Online guest users count
     *
     * @var int
     */
    private $guestsCount;

    /**
     * Get the latest activities of online users
     *
     * @param string|null $type
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function activities($type = null)
    {
        $activities = Activity::typeViewed()
            ->lastFifteenMinutes();

        if ($type == 'guest') {
            $activities->byGuests();
        } elseif ($type == 'member') {
            $activities->byMembers();
        }

        $latestActivityIdPerGroup = $activities
            ->select(DB::raw('max(id) as latest_activity_id'))
            ->groupBy('guest_id', 'user_id');

        return Activity::whereIn('id', $latestActivityIdPerGroup)
            ->with('user', 'subject')
            ->latest()
            ->paginate(static::ACTIVITIES_PER_PAGE);
    }

    /**
     * Get the number of online registered users
     *
     * @return int
     */
    public function membersCount()
    {
        $this->membersCount = Activity::byMembers()
            ->typeViewed()
            ->lastFifteenMinutes()
            ->distinct('user_id')
            ->count();

        return $this->membersCount;
    }

    /**
     * Get the number of online guest suers
     *
     * @return int
     */
    public function guestsCount()
    {
        $this->guestsCount = Activity::byGuests()
            ->typeViewed()
            ->lastFifteenMinutes()
            ->distinct('guest_id')
            ->count();

        return $this->guestsCount;
    }

    /**
     * Get the total number of online users
     *
     * @return int
     */
    public function totalUsersCount()
    {
        $guestsCount = $this->guestsCount ?? $this->guestsCount();
        $membersCount = $this->membersCount ?? $this->membersCount();

        return $guestsCount + $membersCount;
    }

}