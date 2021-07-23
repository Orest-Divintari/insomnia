<?php

namespace Tests\Setup;

use App\Models\ProfilePost;
use App\Models\User;
use Tests\Setup\PostFactory;

class ProfilePostFactory extends PostFactory
{

    protected $poster;
    protected $profileOwner;

    public function create($attributes = [])
    {
        $this->attributes = $attributes;
        $profilePost = ProfilePost::factory()->create(
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
        $profilePosts = ProfilePost::factory()->count($count)->create(
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
        return $this->profileOwner->id ?? User::factory()->create()->id;
    }

    public function toProfile($user)
    {
        $this->profileOwner = $user;
        return $this;
    }
}