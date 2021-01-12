<?php

namespace Tests\Setup;

use App\ProfilePost;
use App\User;
use Tests\Setup\PostFactory;

class ProfilePostFactory extends PostFactory
{

    private $poster;
    private $profileOwner;

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $profilePost = factory(ProfilePost::class)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'profile_owner_id' => $this->profileOwnerId(),
                    'created_at' => $this->getCreatedAt(),
                    'updated_at' => $this->getUpdatedAt(),
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $profilePost;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->attributes = $attributes;
        $profilePosts = factory(ProfilePost::class, $count)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'profile_owner_id' => $this->profileOwnerId(),
                    'created_at' => $this->getCreatedAt(),
                    'updated_at' => $this->getUpdatedAt(),
                    'body' => $this->getBody(),
                ],
                $attributes
            ));
        $this->resetAttributes();
        return $profilePosts;
    }

    private function profileOwnerId()
    {
        if (array_key_exists('profile_owner_id', $this->attributes)) {
            return;
        }
        return $this->profileOwner->id ?? factory(User::class)->create()->id;
    }

    public function toProfile($user)
    {
        $this->profileOwner = $user;
        return $this;
    }
}