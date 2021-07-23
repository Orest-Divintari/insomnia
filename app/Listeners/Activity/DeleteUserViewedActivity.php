<?php

namespace App\Listeners\Activity;

use App\Models\Activity;
use Illuminate\Auth\Events\Logout;

class DeleteUserViewedActivity
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
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        Activity::typeViewed()
            ->where('user_id', auth()->id())
            ->delete();
    }
}
