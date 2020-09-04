<?php

namespace App\Events\Profile;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCommentWasAddedToProfilePost
{
    use Dispatchable, SerializesModels;

    public $profilePost;
    public $comment;
    public $commentPoster;
    public $profileOwner;

    /**
     * Create a new event instance.
     *
     *
     * @param ProfilePost $profilePost
     * @param Reply $comment
     * @param User $commentPoster
     * @param User $profileOwner
     * @return void
     */
    public function __construct($profilePost, $comment, $commentPoster, $profileOwner)
    {
        $this->profilePost = $profilePost;
        $this->comment = $comment;
        $this->commentPoster = $commentPoster;
        $this->profileOwner = $profileOwner;
    }

}