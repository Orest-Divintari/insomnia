<?php

namespace Tests\Setup;

use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

abstract class PostFactory
{
    protected $user = null;
    protected $createdAt;
    protected $updatedAt;
    protected $body;
    protected $attributes;

    abstract public function create($attributes = []);
    abstract public function createMany($count = 1, $attributes = []);

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

    public function createdAt($date)
    {
        $this->createdAt = $date;
        return $this;
    }

    public function updatedAt($date)
    {
        $this->updatedAt = $date;
        return $this;
    }

    public function withBody($body)
    {
        $this->body = $body;
        return $this;
    }

    protected function getBody()
    {
        return $this->body ?? app(Faker::class)->sentence();
    }

    protected function getCreatedAt()
    {
        return $this->createdAt ?? Carbon::now();
    }

    protected function getUpdatedAt()
    {
        return $this->updatedAt ?? Carbon::now();
    }

    protected function userId()
    {
        if ($this->userIdInAttributes()) {
            return;
        }
        return $this->user->id ?? factory(User::class)->create()->id;
    }

    /**
     * Determine whether the user_id key is in the attributes
     *
     * @return bool
     */
    public function userIdInAttributes()
    {
        return (array_key_exists('user_id', $this->attributes));
    }

    /**
     * Determine whether the repliable_id is in the attributes
     *
     * @return bool
     */
    protected function repliableIdInAttributes()
    {
        return array_key_exists('repliable_id', $this->attributes);
    }

    protected function getRepliableId()
    {
        return $this->attributes['repliable_id'];
    }
    protected function resetAttributes()
    {
        foreach (get_object_vars($this) as $attribute => $value) {
            $this->$attribute = null;
        }
    }

}