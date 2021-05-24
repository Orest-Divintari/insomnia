<?php

namespace Tests\Browser\Profile;

use App\User;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StartConversationButton extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function members_may_not_see_the_start_conversation_button_on_their_own_profile()
    {
        $john = create(User::class);
        $john->allowNoone('start_conversation');
        ThreadFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($john) {
            $browser->loginAs($john)
                ->visit('/forum')
                ->assertSee($john->name)
                ->mouseover('@profile-popover')
                ->waitFor('@profile-popover-content')
                ->assertMissing('@start-conversation-button');
        });
    }

    /** @test */
    public function members_may_not_see_the_start_conversation_button_on_profile_popober_when_a_user_does_not_allow_to_have_conversations()
    {
        $john = create(User::class);
        $john->allowNoone('start_conversation');
        ThreadFactory::by($john)->create();

        $george = create(User::class);

        $this->browse(function (Browser $browser) use ($george, $john) {
            $browser->loginAs($george)
                ->visit('/forum')
                ->assertSee($john->name)
                ->mouseover('@profile-popover')
                ->waitFor('@profile-popover-content')
                ->assertMissing('@start-conversation-button');
        });
    }

    /** @test */
    public function members_may_see_the_start_conversation_button_on_profile_popover_when_a_user_allows_it()
    {
        $john = create(User::class);
        $john->allowMembers('start_conversation');
        ThreadFactory::by($john)->create();

        $george = create(User::class);

        $this->browse(function (Browser $browser) use ($george, $john) {
            $browser->loginAs($george)
                ->visit('/forum')
                ->assertSee($john->name)
                ->mouseover('@profile-popover')
                ->waitFor('@profile-popover-content')
                ->assertPresent('@start-conversation-button');
        });
    }

    /** @test */
    public function members_may_see_the_start_conversation_button_on_profile_popover_if_are_followed_by_that_user()
    {
        $john = create(User::class);
        $john->allowFollowing('start_conversation');
        ThreadFactory::by($john)->create();
        $george = create(User::class);
        $john->follow($george);

        $this->browse(function (Browser $browser) use ($george, $john) {
            $browser->loginAs($george)
                ->visit('/forum')
                ->assertSee($john->name)
                ->mouseover('@profile-popover')
                ->waitFor('@profile-popover-content')
                ->assertPresent('@start-conversation-button');
        });
    }

    /** @test */
    public function members_may_not_see_the_start_conversation_button_on_profile_popover_if_are_not_followed_by_that_user()
    {
        $john = create(User::class);
        $john->allowFollowing('start_conversation');
        ThreadFactory::by($john)->create();
        $george = create(User::class);

        $this->browse(function (Browser $browser) use ($george, $john) {
            $browser->loginAs($george)
                ->visit('/forum')
                ->assertSee($john->name)
                ->mouseover('@profile-popover')
                ->waitFor('@profile-popover-content')
                ->assertMissing('@start-conversation-button');
        });
    }
}