<?php

namespace Tests\Setup;

use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class NotificationFactory
{

    use WithFaker;

    public function forUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function withDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function create()
    {
        return $this->user->notifications()->create([
            'id' => Str::uuid(),
            'data' => app(Faker::class)->word,
            'type' => app(Faker::class)->word,
            'read_at' => null,
            'created_at' => $this->date ?? now(),
            'updated_at' => $this->date ?? now(),
        ]);
    }
}