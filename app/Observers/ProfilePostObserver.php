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
    public function deleted(ProfilePost $profilePost)
    {
        $profilePost->comments->each->delete();
    }

}