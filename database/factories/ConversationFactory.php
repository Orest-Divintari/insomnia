<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Conversation;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Conversation::class, function (Faker $faker) {
    $title = $faker->sentence();
    $slug = Str::slug($title);
    return [
        'user_id' => auth()->id() ?: factory(User::class),
        'title' => $title,
        'slug' => $slug,
    ];
});