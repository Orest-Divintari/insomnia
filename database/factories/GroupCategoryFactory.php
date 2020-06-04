<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\GroupCategory;
use Faker\Generator as Faker;

$factory->define(GroupCategory::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(),
        'excerpt' => $faker->sentence(),
    ];
});