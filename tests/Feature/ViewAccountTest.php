<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_the_account()
    {
        $user = $this->signIn();

        $response = $this->get(route('account'));

        $response->assertViewHas(compact('user'));
    }
}