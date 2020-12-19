<?php

namespace App\Listeners\Follow;

use App\Events\Follow\AUserUnfollowedYou;
use App\Notifications\YouHaveANewFollower;
use App\User;

class DeleteFollowNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AUserUnfollowedYou  $event
     * @return void
     */
    public function handle(AUserUnfollowedYou $event)
    {
        $event->following->notifications()
            ->where([
                'notifiable_type' => User::class,
                "type" => YouHaveANewFollower::class,
            ])->whereJsonContains('data->follower_id', $event->follower->id)
            ->delete();
    }
}