<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Conversation;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Conversation::class, function (Faker $faker) {
    $title = $faker->sentence();
    $slug = Str::slug($title);
    return [
        'title' => $title,
        'slug' => $slug,
    ];
});