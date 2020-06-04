<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Authenticates user
     *
     * If a user is given, authenticates that user
     * Otherwise, authenticates a newly created user
     *
     * @param User $user|null $user
     * @return User $user
     */
    protected function signIn($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $this->actingAs($user);
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
        $user = $user ?: factory(User::class)->create(['email' => config('insomnia.administrators')[0]]);

        $this->actingAs($user);
        return $user;
    }
}