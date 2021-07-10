<?php

namespace Tests\Feature\Admin\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewDashBoardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_should_not_see_the_admin_dashboard()
    {
        $response = $this->get(route('admin.dashboard.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorised_users_should_not_see_the_admin_dashboard()
    {
        $this->signIn();

        $response = $this->get(route('admin.dashboard.index'));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function admins_may_see_the_dashboard()
    {
        $this->signInAdmin();

        $response = $this->get(route('admin.dashboard.index'));

        $response->assertOk();
    }
}