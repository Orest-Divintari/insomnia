<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostWasAddedToProfile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $profileUser;
    public $poster;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($post, $profileUser, $poster)
    {
        $this->post = $post;
        $this->profileUser = $profileUser;
        $this->poster = $poster;
    }
}