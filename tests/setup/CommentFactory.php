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

        $profilePost = $this->createProfilePost($attributes);

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

    public function createProfilePost($attributes)
    {
        if (array_key_exists('repliable_id', $attributes)) {
            $profilePost = ProfilePost::find($attributes['repliable_id']);
        } else {
            $profilePost = create(ProfilePost::class);
        }
        return $profilePost;
    }

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}