<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfilePostWasUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($profilePost, $postPoster)
    {
        $this->profilePost = $profilePost;
        $this->postPoster = $postPoster;
        $this->profileOwner = $profilePost->profileOwner;
    }

}