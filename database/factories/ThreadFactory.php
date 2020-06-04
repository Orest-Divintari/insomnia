<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use App\Thread;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Thread::class, function (Faker $faker) {
    $title = $faker->sentence();
    return [
        'title' => $title,
        'slug' => Str::slug($title),
        'body' => $faker->sentence(),
        'user_id' => factory(User::class),
        'category_id' => factory(Category::class),
        'pinned' => false,
        'locked' => false,
        'replies_count' => 0,
    ];
});