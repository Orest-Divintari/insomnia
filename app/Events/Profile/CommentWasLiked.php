<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentWasLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $commentPoster;
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
     * @param User $commentPoster
     * @param ProfilePost $profilePost
     * @param User $profileOwner
     *
     * @return void
     */
    public function __construct($liker, $like, $comment, $commentPoster, $profilePost, $profileOwner)
    {
        $this->liker = $liker;
        $this->like = $like;
        $this->comment = $comment;
        $this->commentPoster = $commentPoster;
        $this->profilePost = $profilePost;
        $this->profileOwner = $profileOwner;
    }

}