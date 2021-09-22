<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_recapha_is_required()
    {
        $user = $this->signInUnverified();

        $response = $this->postJson(route('ajax.verification-email.store'), [
            'g-recaptcha-response' => '',
        ]);

        $response->assertJson(['g-recaptcha-response' => ['The g-recaptcha-response field is required.']]);
    }

    /** @test */
    public function it_should_not_resend_verification_email_for_verified_users()
    {
        $user = $this->signIn();

        $response = $this->postJson(route('ajax.verification-email.store'), [
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function it_resends_email_verification_email_for_unvefiried_users()
    {
        $user = User::factory()->unverified()->create();
        $this->signIn($user);
        $this->mock(Recaptcha::class, function ($mock) {
            $mock->shouldReceive('passes')->andReturn(true);
        });
        Notification::fake();

        $response = $this->post(route('ajax.verification-email.store'), [
            'g-recaptcha-response' => 'some token',
        ]);

        $response->assertOk();
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}