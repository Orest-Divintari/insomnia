<?php

namespace Tests\Setup;

use App\ProfilePost;
use App\Reply;
use Tests\Setup\PostFactory;

class CommentFactory extends PostFactory
{

    private $profilePost;
    protected $user;
    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $comment = factory(Reply::class)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'repliable_id' => $this->profilePostId(),
                    'repliable_type' => ProfilePost::class,
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $comment;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->attributes = $attributes;
        $comments = factory(Reply::class, $count)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'repliable_id' => $this->profilePostId(),
                    'repliable_type' => ProfilePost::class,
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $comments;
    }

    private function profilePostId()
    {
        if ($this->repliableIdInAttributes()) {
            return;
        }
        return $this->profilePost->id ?? factory(ProfilePost::class)->create()->id;
    }

    public function toProfilePost($profilePost)
    {
        $this->profilePost = $profilePost;
        return $this;
    }
}