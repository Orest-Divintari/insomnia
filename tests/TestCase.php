<?php

namespace Tests;

use App\Http\Middleware\AppendVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\Traits\RefreshRedis;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshRedis;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AppendVisitor::class);
        $this->refreshRedis();
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
        $this->actingAs($user);
        return $user;
    }

    /**
     * Set MySQL database for testing
     *
     * @return void
     */
    public function useMysql()
    {
        $test = 'mysql_test';
        config(['database.default' => $test]);
        Artisan::call('migrate:refresh --database=' . $test);
    }
}