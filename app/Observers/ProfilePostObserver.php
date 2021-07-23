<?php

namespace App\Observers;

use App\Models\ProfilePost;

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
        $profilePost->likes->each->delete();
        $profilePost->activities->each->delete();
        $profilePost->comments->each->delete();
    }

}
