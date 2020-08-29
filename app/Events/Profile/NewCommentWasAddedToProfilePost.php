<?php

namespace App\Events\Profile;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCommentWasAddedToProfilePost
{
    use Dispatchable, SerializesModels;

    public $post;
    public $comment;
    public $commentPoster;
    public $profileUser;

    /**
     * Create a new event instance.
     *
     *
     * @param ProfilePost $post
     * @param User $profileUser
     * @param User $poster
     * @return void
     */
    public function __construct($post, $comment, $commentPoster, $profileUser)
    {
        $this->post = $post;
        $this->comment = $comment;
        $this->commentPoster = $commentPoster;
        $this->profileUser = $profileUser;
    }

}