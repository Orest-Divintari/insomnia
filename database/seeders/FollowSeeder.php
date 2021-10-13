<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    use RandomModels, AuthenticatesUsers;

    const FOLLOWINGS = 1;
    const FOLLOWERS = 1;
    const NUMBER_OF_USERS = 1;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->randomUsers(static::NUMBER_OF_USERS)->each(function ($user) {
            $randomUsers = $this->randomUsersExcept(static::NUMBER_OF_USERS, $user);
            $randomUsers->each(function ($randomUser) use ($user) {
                $user->follow($randomUser);
            });
        });
    }
}