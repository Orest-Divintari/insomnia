<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = 'example123';

        $users = ['uric', 'orestis', 'urielakos'];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user,
                'email' => $user . '@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt($password),
            ]);
        }
    }
}