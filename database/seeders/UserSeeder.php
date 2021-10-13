<?php
namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = ['john', 'uric', 'orestis', 'urielakos'];
        Role::create(['name' => 'admin']);
        foreach ($users as $user) {
            $user = User::create([
                'name' => $user,
                'email' => $user . '@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'email_verified_at' => Carbon::now(),
                'password' => 'example123',
            ]);

            $user->assignRole('admin');
        }
    }
}