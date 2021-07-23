<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(Recaptcha::class, function ($mock) {
            $mock->shouldReceive('passes')->andReturn(true);
        });
    }

    public function user_registration_requires_recaptcha()
    {
        $this->withoutExceptionHandling();
        // unset the mocking Recaptcha
        // in order to use the real Recaptcha class
        unset(app()[Recaptcha::class]);
        $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])->assertSessionHasErrors('g-recaptcha-response');
    }

    public function a_verification_email_is_sent_when_user_is_registered()
    {
        Event::fake();

        $this->post(route('register'), [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'g-recaptcha-response' => 'some token',
        ])->assertSessionHasErrors('g-recaptcha-response');

        Event::assertDispatched(Registered::class);
    }

    public function a_registered_user_has_to_manually_verify_the_email()
    {
        $this->withoutExceptionHandling();
        $this->post('/register', [
            'name' => 'orest',
            'email' => 'qq@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'g-recaptcha-response' => 'some token',
        ]);

        $this->assertNull(User::first()->email_verified_at);

    }

}
