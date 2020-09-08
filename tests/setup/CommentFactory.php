<?php

namespace Tests\Setup;

use App\ProfilePost;
use App\Reply;
use App\User;

class CommentFactory
{

    protected $user;

    public function create()
    {
        $this->user = $this->user ?: factory(User::class)->create();

        $profilePost = factory(ProfilePost::class)->create();

        $comment = factory(Reply::class)->create([
            'user_id' => $this->user->id,
            'repliable_id' => $profilePost->id,
            'repliable_type' => ProfilePost::class,
        ]);

        return $comment;
    }

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}