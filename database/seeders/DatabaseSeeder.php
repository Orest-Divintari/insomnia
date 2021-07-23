<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ForumSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TagSeeder::class);
    }
}