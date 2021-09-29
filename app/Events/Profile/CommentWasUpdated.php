<?php

namespace App\Events\Profile;

class CommentWasUpdated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment, $commentPoster)
    {
        $this->comment = $comment;
        $this->commentPoster = $commentPoster;
        $this->profilePost = $comment->repliable;
        $this->profileOwner = $comment->repliable->profileOwner;
    }
}