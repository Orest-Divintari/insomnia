<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Reply;
use App\Thread;
use App\User;
use Faker\Generator as Faker;

$factory->define(Reply::class, function (Faker $faker) {
    return [
        'body' => $faker->paragraph(),
        'repliable_id' => factory(Thread::class),
        'repliable_type' => 'App\Thread',
        'user_id' => factory(User::class),
    ];
});