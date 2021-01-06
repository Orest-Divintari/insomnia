<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Category::class, function (Faker $faker) {
    $title = $faker->sentence();
    return [
        'title' => $title,
        'slug' => Str::slug($title),
        'excerpt' => $faker->sentence(),
        'parent_id' => null,
        'group_category_id' => null,
        'avatar_path' => null,
        //
    ];
});