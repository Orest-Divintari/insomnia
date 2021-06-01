<?php

namespace Tests\Browser\Profile;

use App\User;
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

    /** @test */
    public function the_profile_owner_may_always_see_the_contact_identities()
    {
        $profileOwner = create(User::class);
        $facebookName = 'orestis';
        $profileOwner->details()->merge(['facebook' => $facebookName]);

        $this->browse(function (Browser $browser) use ($profileOwner, $facebookName) {

            $response = $browser->loginAs($profileOwner)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response
                ->assertSee('Contact')
                ->assertSee('Facebook')
                ->assertSee($facebookName);
        });
    }

    /** @test */
    public function noone_can_see_the_contact_identities_of_a_user_if_that_user_does_not_allow_it()
    {
        $profileOwner = create(User::class);
        $facebookName = 'orestis';
        $profileOwner->details()->merge(['facebook' => $facebookName]);
        $profileOwner->allowNoone('show_identities');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($profileOwner, $facebookName, $visitor) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink("About");

            $response
                ->assertDontSee('Contact')
                ->assertDontSee('Facebook')
                ->assertDontSee($facebookName);
        });
    }

    /** @test */
    public function a_member_may_see_the_contact_identities_of_a_user_if_the_user_allows_it()
    {
        $profileOwner = create(User::class);
        $facebookName = 'orestis';
        $profileOwner->details()->merge(['facebook' => $facebookName]);
        $profileOwner->allowMembers('show_identities');
        $visitor = create(User::class);

        $this->browse(function (Browser $browser) use ($profileOwner, $facebookName, $visitor) {

            $response = $browser->loginAs($visitor)
                ->visit("/profiles/{$profileOwner->name}")
                ->clickLink('About');

            $response
                ->assertSee('Contact')
                ->assertSee('Facebook')
                ->assertSee($facebookName);
        });
    }
}