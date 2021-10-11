<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentWasLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $poster;
    public $profilePost;
    public $profileOwner;
    public $liker;
    public $like;

    /**
     * Create a new event instance.
     *
     * @param User $liker
     * @param Like $like
     * @param Reply $comment
     * @param User $poster
     * @param ProfilePost $profilePost
     * @param User $profileOwner
     *
     * @return void
     */
    public function __construct($liker, $like, $comment, $poster, $profilePost, $profileOwner)
    {
        $this->liker = $liker;
        $this->like = $like;
        $this->comment = $comment;
        $this->poster = $poster;
        $this->profilePost = $profilePost;
        $this->profileOwner = $profileOwner;
    }

}