<?php

namespace Tests;

use App\Http\Middleware\AppendVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AppendVisitor::class);
    }

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
        $user = $user ?: User::factory()->create();
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
        Role::create(['name' => 'admin']);
        $user = $user ?: User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        return $user;
    }
}