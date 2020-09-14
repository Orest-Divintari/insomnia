<?php

namespace Tests\Setup;

use App\ProfilePost;
use App\Reply;
use App\User;

class CommentFactory
{

    protected $user;

    public function create($attributes = [])
    {
        $this->user = $this->user ?: factory(User::class)->create();

        $profilePost = factory(ProfilePost::class)->create();

        $comment = factory(Reply::class)->create(
            array_merge(
                [
                    'user_id' => $this->user->id,
                    'repliable_id' => $profilePost->id,
                    'repliable_type' => ProfilePost::class,
                ],
                $attributes
            ));

        return $comment;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->user = $this->user ?: factory(User::class)->create();

        $profilePost = factory(ProfilePost::class)->create();

        $comments = factory(Reply::class, $count)->create(
            array_merge(
                [
                    'user_id' => $this->user->id,
                    'repliable_id' => $profilePost->id,
                    'repliable_type' => ProfilePost::class,
                ],
                $attributes
            ));

        return $comments;
    }

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}