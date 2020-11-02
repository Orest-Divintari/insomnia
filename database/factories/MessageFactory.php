<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Conversation;
use App\Message;
use App\User;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'conversation_id' => factory(Conversation::class),
        'user_id' => auth()->id() ?: factory(User::class),
        'body' => $faker->text(),
    ];
});