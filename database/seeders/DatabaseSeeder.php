<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Notification::fake();

        $this->call(ForumSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(ProfilePostSeeder::class);
        $this->call(FollowSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(ConversationSeeder::class);
        $this->call(LikeSeeder::class);
        $this->call(ReadSeeder::class);
        $this->call(IgnorationSeeder::class);
        $this->call(ThreadSubsriptionSeeder::class);

        Auth::logout();
    }
}