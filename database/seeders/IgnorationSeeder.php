<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IgnorationSeeder extends Seeder
{
    use RandomModels;

    const NUMBER_OF_USERS = 1;
    const NUMBER_OF_THREADS = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = $this->randomUsers(static::NUMBER_OF_USERS);

        $ignoredUsers = $this->randomUsersExcept(static::NUMBER_OF_USERS, $users);
        $ignoredThreads = $this->randomThreads(static::NUMBER_OF_THREADS);

        $this->ignoreUsers($users, $ignoredUsers);
        $this->ignoreThreads($users, $ignoredThreads);
    }

    protected function ignoreUsers($users, $ignoredUsers)
    {
        $users->each(function ($user) use ($ignoredUsers) {
            $ignoredUsers->each(function ($ignoredUser) use ($user) {
                $user->ignore($ignoredUser);
            });
        });
    }

    protected function ignoreThreads($users, $ignoredThreads)
    {
        $users->each(function ($user) use ($ignoredThreads) {
            $ignoredThreads->each(function ($ignoredThread) use ($user) {
                $user->ignore($ignoredThread);
            });
        });
    }
}