<?php

namespace App\Events\Follow;

use App\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AUserStartedFollowingYou
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that has new follower
     *
     * @var User
     */
    public $following;

    /**
     * The user who followed
     *
     * @var User
     */
    public $follower;

    /**
     * @var Carbon
     */
    public $followDate;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $follower, User $following, Carbon $followDate)
    {
        $this->follower = $follower;
        $this->following = $following;
        $this->followDate = $followDate;
    }
}