<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $currentPassword;
    protected $newPassword;

    public function setUp(): void
    {
        parent::setUp();
        $this->currentPassword = 'example123';
        $this->newPassword = 'newpassword';
    }

    /** @test */
    public function a_user_can_change_the_password()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $this->patch(
            route('account.password.update'),
            [
                'current_password' => $this->currentPassword,
                'password' => $this->newPassword,
                'password_confirmation' => $this->newPassword,
            ]
        );

        $this->assertTrue(Hash::check($this->newPassword, $user->fresh()->password));
    }

    /** @test */
    public function the_current_password_is_required()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => '',
                'password' => $this->newPassword,
                'password_confirmation' => $this->newPassword,
            ]
        );

        $response->assertSessionHasErrors('current_password');
    }

    /** @test */
    public function the_current_password_must_match_the_existing_password_in_the_database()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => 'randomPassword',
                'password' => $this->newPassword,
                'password_confirmation' => $this->newPassword,
            ]
        );

        $response->assertSessionHasErrors('current_password');
    }

    /** @test */
    public function the_current_password_must_be_at_least_8_characters()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => 'car',
                'password' => $this->newPassword,
                'password_confirmation' => $this->newPassword,
            ]
        );

        $response->assertSessionHasErrors('current_password');
    }

    /** @test */
    public function the_new_password_is_required()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => $this->currentPassword,
                'password' => '',
                'password_confirmation' => $this->newPassword,
            ]
        );

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_new_password_must_be_at_least_8_characters()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => $this->currentPassword,
                'password' => 'car',
                'password_confirmation' => $this->newPassword,
            ]
        );

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_confirmation_of_the_new_password_is_required()
    {
        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => $this->currentPassword,
                'password' => $this->newPassword,
                'password_confirmation' => '',
            ]
        );

        $response->assertSessionHasErrors('password_confirmation');
    }

    /** @test */
    public function the_new_password_confirmation_must_match_the_new_password()
    {

        $user = create(User::class, ['password' => $this->currentPassword]);
        $this->signIn($user);

        $response = $this->patch(
            route('account.password.update'),
            [
                'current_password' => $this->currentPassword,
                'password' => $this->newPassword,
                'password_confirmation' => $this->currentPassword,
            ]
        );

        $response->assertSessionHasErrors('password');
    }
}
