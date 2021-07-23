<?php

namespace Tests\Browser\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function members_may_see_the_profile_information_of_a_user()
    {
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit("/profiles/{$user->name}")
                ->assertSee($user->name)
                ->assertSee('Macrumors newbie')
                ->assertSee('Messages')
                ->assertSeeIn('@messages-count', 0)
                ->assertSee('Likes Score')
                ->assertSeeIn('@likes-count', 0)
                ->assertSee('Points')
                ->assertSeeIn('@points', 0)
                ->assertSee('Joined')
                ->assertSee($user->join_date)
                ->assertSeeLink($user->name);
        });
    }
}
