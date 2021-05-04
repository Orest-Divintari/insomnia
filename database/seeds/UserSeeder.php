<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
            User::create([
                'name' => $user,
                'email' => $user . '@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt($password),
            ]);
        }
    }
}