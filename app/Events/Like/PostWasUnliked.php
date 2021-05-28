<?php

namespace App\Events\Like;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostWasUnliked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $likedId;

    /**
     * The post might be
     *
     * comment
     * thread-reply
     * message
     * profile-post
     *
     * @var mixed
     */
    public $post;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($post, $likeId)
    {
        $this->post = $post;
        $this->likeId = $likeId;
    }

}