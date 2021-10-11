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
    public function __construct($profilePost, $poster)
    {
        $this->profilePost = $profilePost;
        $this->poster = $poster;
        $this->profileOwner = $profilePost->profileOwner;
    }

}