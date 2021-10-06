<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    use RandomModels, AuthenticatesUsers;

    const FOLLOWINGS = 5;
    const FOLLOWERS = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->randomUsers(2000)->each(function ($user) {
            $randomUsers = $this->randomUsersExcept(1000, $user);
            $randomUsers->each(function ($randomUser) use ($user) {
                $user->follow($randomUser);
            });
        });
    }
}