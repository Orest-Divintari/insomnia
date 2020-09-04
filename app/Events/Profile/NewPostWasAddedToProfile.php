<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostWasAddedToProfile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $profilePost;
    public $profileOwner;
    public $postPoster;
    /**
     * Create a new event instance.
     *
     * @param ProfilePost $profilePost
     * @param User $postPoster
     * @param User $profileOwner
     *
     * @return void
     */
    public function __construct($profilePost, $postPoster, $profileOwner)
    {
        $this->profilePost = $profilePost;
        $this->postPoster = $postPoster;
        $this->profileOwner = $profileOwner;
    }
}