<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ThreadSubsriptionSeeder extends Seeder
{
    use RandomModels;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = $this->randomUsers(2000);

        $threads = $this->randomThreads(2000);

        $threads->each(function ($thread) use ($users) {
            $users->each(function ($user) use ($thread) {
                $thread->subscribe($user->id);
            });
        });
    }
}