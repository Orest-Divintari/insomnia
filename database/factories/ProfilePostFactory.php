<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ProfilePost;
use App\User;
use Faker\Generator as Faker;

$factory->define(ProfilePost::class, function (Faker $faker) {
    return [
        'body' => $faker->sentence(),
        'profile_owner_id' => factory(User::class),
        'user_id' => auth()->id() ?? factory(User::class),
    ];
});