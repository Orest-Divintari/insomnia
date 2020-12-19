<?php

namespace App\Events\Follow;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AUserUnfollowedYou
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user who unfollowed the other user
     *
     * @var User
     */
    public $follower;

    /**
     * The user who got unfollowed
     *
     * @var User
     */
    public $following;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($follower, $following)
    {
        $this->follower = $follower;
        $this->following = $following;
    }
}