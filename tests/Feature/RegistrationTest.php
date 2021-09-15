<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(Recaptcha::class, function ($mock) {
            $mock->shouldReceive('passes')->andReturn(true);
        });
    }

    /** @test */
    public function user_registration_requires_recaptcha()
    {
        $password = 'H3Ll0@FRIend';
        // unset the mocking Recaptcha
        // in order to use the real Recaptcha class
        unset(app()[Recaptcha::class]);

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_verification_email_is_sent_when_user_is_registered()
    {
        Event::fake();
        $password = 'H3Ll0@friend';

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        Event::assertDispatched(Registered::class);
    }

    /** @test */
    public function a_registered_user_has_to_manually_verify_the_email()
    {
        $password = 'H3Ll0@friend';

        $this->post('/register', [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        $this->assertNull(User::first()->email_verified_at);
    }

    /** @test */
    public function the_password_must_be_of_at_lease_8_characters()
    {
        $password = 'h3lL0@';

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_have_at_least_one_uppercase_letter()
    {
        $password = 'h3ll0@friend';

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_have_at_least_one_lowercase_letter()
    {
        $password = 'H3LL0@FRIEND';

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_have_at_least_one_number()
    {
        $password = 'HELLO@friend';

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_have_at_least_one_symbol()
    {
        $password = 'HELLOfriend2';

        $response = $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertSessionHasErrors('password');
    }
}