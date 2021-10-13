<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ThreadSubsriptionSeeder extends Seeder
{
    use RandomModels;

    const NUMBER_OF_USERS = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = $this->randomUsers(static::NUMBER_OF_USERS);

        $threads = $this->randomThreads(static::NUMBER_OF_USERS);

        $threads->each(function ($thread) use ($users) {
            $users->each(function ($user) use ($thread) {
                $thread->subscribe($user->id);
            });
        });
    }
}