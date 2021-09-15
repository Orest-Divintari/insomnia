<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_may_login_using_the_email()
    {
        $user = create(User::class, ['name' => 'azem', 'password' => 'sesameOpen']);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'sesameOpen',
        ]);

        $response->assertRedirect(route('forum'));
    }

    /** @test */
    public function a_user_may_login_using_the_name()
    {
        $name = 'azem';
        $user = create(User::class, ['name' => $name, 'password' => 'sesameOpen']);

        $response = $this->post(route('login'), [
            'email' => $name,
            'password' => 'sesameOpen',
        ]);

        $response->assertRedirect(route('forum'));
    }
}