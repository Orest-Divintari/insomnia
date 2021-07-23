<?php

namespace App\ViewModels;

use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Spatie\ViewModels\ViewModel;

class OnlineUserActivitiesViewModel extends ViewModel
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
     * The type of user
     *
     * @var string|null
     */
    protected $type;

    /**
     * Create a new view model instance
     *
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * Get the latest activities of online users
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function activities()
    {
        $activities = Activity::typeViewed()
            ->lastFifteenMinutes();

        if ($this->type == 'guest') {
            $activities->byGuests();
        } elseif ($this->type == 'member') {
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

    /**
     * Get the type of the user
     *
     * @return string|null
     */
    public function type()
    {
        return $this->type;
    }
}
