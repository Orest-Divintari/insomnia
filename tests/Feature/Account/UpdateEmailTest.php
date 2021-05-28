<?php

namespace Tests\Feature\Account;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_update_the_email()
    {
        $password = 'example123';
        $user = create(User::class, compact('password'));
        $this->signIn($user);
        $email = 'orestis@yahoo.com';

        $this->patch(route('ajax.user-email.update', $user), compact('email', 'password'));

        $this->assertEquals($email, $user->email);
    }

    /** @test */
    public function the_user_must_enter_the_existing_password_to_update_the_email()
    {
        $user = $this->signIn();
        $email = 'orestis@yahoo.com';
        $password = 'random password';

        $response = $this->json('patch', route('ajax.user-email.update', $user), compact('email', 'password'));

        $this->assertNotEquals($email, $user->fresh()->email);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['password' => ['Your existing password is not correct.']]);
    }

    /** @test */
    public function the_new_email_must_be_unique()
    {
        $password = 'example123';
        $user = create(User::class, compact('password'));
        $email = 'orestis@yahoo.com';
        create(User::class, compact('email'));
        $this->signIn($user);

        $response = $this->json('patch', route('ajax.user-email.update', $user), compact('email', 'password'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertNotEquals($email, $user->fresh()->email);
        $response->assertJson(['email' => ['The email has already been taken.']]);
    }

    /** @test */
    public function the_current_email_is_ignored_by_unique_email_rule()
    {
        $password = 'example123';
        $user = create(User::class, compact('password'));
        create(User::class, ['email' => 'random@email']);
        $this->signIn($user);
        $email = $user->email;

        $response = $this->json('patch', route('ajax.user-email.update', $user), compact('email', 'password'));

        $response->assertOk();
    }
}