<?php

namespace App\Observers;

use App\ProfilePost;

class ProfilePostObserver
{
    /**
     * Handle the profile post "deleted" event.
     *
     * @param  \App\ProfilePost  $profilePost
     * @return void
     */
    public function deleting(ProfilePost $profilePost)
    {
        $profilePost->activities->each->delete();
        $profilePost->comments->each->delete();
    }

}