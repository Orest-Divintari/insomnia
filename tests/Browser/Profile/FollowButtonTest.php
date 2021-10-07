<?php

namespace Tests\Browser\Profile;

use App\Models\User;
use App\ViewModels\ForumViewModel;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FollowButtonTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function setUp(): void
    {
        parent::setUp();
        app(ForumViewModel::class)->resetCache();
    }

    /** @test */
    public function guests_should_not_see_the_follow_button()
    {

        $john = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($john, $threadByJohn) {
            $response = $browser
                ->visit(route('forum'))
                ->assertSee($threadByJohn->title)
                ->mouseover("@{$john->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertVisible('@profile-popover-content')
                ->assertMissing('@follow-button');
        });
    }

    /** @test */
    public function users_cannot_follow_themselves()
    {
        $john = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($john, $threadByJohn) {
            $response = $browser
                ->loginAs($john)
                ->visit(route('forum'))
                ->assertSee($threadByJohn->title)
                ->mouseover("@{$john->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertVisible('@profile-popover-content')
                ->assertMissing('@follow-button');
        });
    }

    /** @test */
    public function unverified_users_should_not_see_the_follow_button()
    {
        $unverifiedUser = User::factory()->unverified()->create();
        $john = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($unverifiedUser, $john, $threadByJohn) {
            $response = $browser
                ->loginAs($unverifiedUser)
                ->visit(route('forum'))
                ->assertSee($threadByJohn->title)
                ->mouseover("@{$john->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertVisible('@profile-popover-content')
                ->assertMissing('@follow-button');
        });
    }

    /** @test */
    public function verified_users_should_see_the_follow_button()
    {
        $verifiedUser = create(User::class);
        $john = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($verifiedUser, $john, $threadByJohn) {
            $response = $browser->loginAs($verifiedUser)
                ->visit(route('forum'))
                ->assertSee($threadByJohn->title)
                ->mouseover("@{$john->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertVisible('@profile-popover-content')
                ->assertVisible('@follow-button');
        });
    }

    /** @test */
    public function authorised_users_may_follow_another_user()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $threadByDoe = ThreadFactory::by($doe)->create();

        $this->browse(function (Browser $browser) use ($doe, $john, $threadByDoe) {
            $response = $browser
                ->loginAs($john)
                ->visit(route('forum'))
                ->assertSee($threadByDoe->title)
                ->mouseover("@{$doe->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertVisible('@profile-popover-content')
                ->assertVisible('@follow-button')
                ->click('@follow-button')
                ->waitFortext('Unfollow')
                ->assertSee('Unfollow');

            $this->assertTrue($john->following($doe));
        });
    }

    /** @test */
    public function authorised_users_may_unfollow_another_user()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $threadByDoe = ThreadFactory::by($doe)->create();
        $john->follow($doe);

        $this->browse(function (Browser $browser) use ($doe, $john, $threadByDoe) {
            $response = $browser
                ->loginAs($john)
                ->visit(route('forum'))
                ->assertSee($threadByDoe->title)
                ->mouseover("@{$doe->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertVisible('@profile-popover-content')
                ->assertVisible('@follow-button')
                ->assertSee('Unfollow')
                ->click('@follow-button')
                ->waitFortext('Follow')
                ->assertSee('Follow');

            $this->assertFalse($john->following($doe));
        });
    }
}