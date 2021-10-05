<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\Role;

trait AuthenticatesUsers
{
    /**
     * Authenticates user
     *
     * If a user is given, authenticates that user
     * Otherwise, authenticates a newly created user
     *
     * @param User $user|null $user
     * @return User
     */
    protected function signIn($user = null)
    {
        Auth::logout();
        $user = $user ?: User::factory()->create();
        Auth::login($user);
        return $user;
    }

    /**
     * Sign in with administrator account
     *
     * If a user is given, it signs in that user as administrator
     * Othwerwise, it creates a new administrator user
     *
     * @param User $user|null $user
     * @return User $user
     */
    protected function signInAdmin($user = null)
    {
        Role::create(['name' => 'admin']);
        $user = $user ?: User::factory()->create();
        $user->assignRole('admin');
        Auth::login($user);
        return $user;
    }

    /**
     * Sign in as unverified user
     *
     * @param User|null $user
     * @return User
     */
    protected function signInUnverified($user = null)
    {
        if ($user instanceof User) {
            $user->update(['email_verified_at' => null]);
        } else {
            $user = User::factory()->unverified()->create();
        }
        Auth::login($user);
        return $user;
    }
}