<?php

namespace App\Events\Profile;

class CommentWasUpdated
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($comment, $poster)
    {
        $this->comment = $comment;
        $this->poster = $poster;
        $this->profilePost = $comment->repliable;
        $this->profileOwner = $comment->repliable->profileOwner;
    }
}