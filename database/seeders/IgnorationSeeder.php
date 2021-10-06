<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IgnorationSeeder extends Seeder
{
    use RandomModels;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = $this->randomUsers(1000);

        $ignoredUsers = $this->randomUsersExcept(1000, $users);
        $ignoredThreads = $this->randomThreads(1000);

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