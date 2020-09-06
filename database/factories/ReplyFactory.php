<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Reply;
use App\User;
use Faker\Generator as Faker;

$factory->define(Reply::class, function (Faker $faker) {
    return [
        'body' => $faker->paragraph(),
        'repliable_id' => null,
        'repliable_type' => 'App\Thread',
        'user_id' => factory(User::class),
    ];

});

$factory->state(Reply::class, 'thread', [
    'repliable_id' => 1,
]);

$factory->state(Reply::class, 'profilePost', [
    'repliable_id' => 1,
    'repliable_type' => 'App\ProfilePost',
]);