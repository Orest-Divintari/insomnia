<?php

namespace App\Events\Subscription;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfilePostWasLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $profilePost;
    public $profileOwner;
    public $poster;
    public $liker;
    public $like;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($liker, $like, $profilePost, $profileOwner, $poster)
    {
        $this->profileOwner = $profileOwner;
        $this->like = $like;
        $this->liker = $liker;
        $this->profilePost = $profilePost;
        $this->poster = $poster;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}