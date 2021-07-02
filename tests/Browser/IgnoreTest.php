<?php

namespace Tests\Browser;

use App\Category;
use App\User;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IgnoreTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function authorised_users_may_ignore_a_thread()
    {
        $category = create(Category::class);
        $john = create(User::class);
        $thread = ThreadFactory::inCategory($category)->by($john)->create();
        $doe = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $doe) {
            $browser->loginAs($doe)
                ->visit(route('threads.show', $thread))
                ->assertVisible('@ignore-thread-button')
                ->assertSee('Ignore')
                ->click('@ignore-thread-button')
                ->waitForText('Unignore')
                ->assertSee('Unignore')
                ->refresh()
                ->assertSee('Unignore');
        });
    }

    /** @test */
    public function authorised_users_may_unignore_a_thread()
    {
        $category = create(Category::class);
        $john = create(User::class);
        $thread = ThreadFactory::inCategory($category)->by($john)->create();
        $doe = create(User::class);
        $thread->markAsIgnored($doe);

        $this->browse(function (Browser $browser) use ($thread, $doe) {
            $browser->loginAs($doe)
                ->visit(route('threads.show', $thread))
                ->assertVisible('@ignore-thread-button')
                ->assertSee('Unignore')
                ->click('@ignore-thread-button')
                ->waitForText('Ignore')
                ->assertSee('Ignore')
                ->refresh()
                ->assertSee('Ignore');
        });
    }

    /** @test */
    public function authorised_users_may_ignore_another_user()
    {
        $john = create(User::class);
        $doe = create(User::class);

        $this->browse(function (Browser $browser) use ($john, $doe) {
            $browser->loginAs($john)
                ->visit(route('profiles.show', $doe))
                ->assertVisible('@ignore-user-button')
                ->assertSee('Ignore')
                ->click('@ignore-user-button')
                ->waitForText('Unignore')
                ->assertSee('Unignore')
                ->refresh()
                ->assertSee('Unignore');
        });
    }

    /** @test */
    public function authorised_users_may_unignore_another_user()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $doe->markAsIgnored($john);

        $this->browse(function (Browser $browser) use ($john, $doe) {
            $browser->loginAs($john)
                ->visit(route('profiles.show', $doe))
                ->assertVisible('@ignore-user-button')
                ->assertSee('Unignore')
                ->click('@ignore-user-button')
                ->waitForText('Ignore')
                ->assertSee('Ignore')
                ->refresh()
                ->assertSee('Ignore');
        });
    }

    /** @test */
    public function users_should_not_see_the_ignore_button_on_their_own_proifle()
    {
        $john = create(User::class);

        $this->browse(function (Browser $browser) use ($john) {
            $browser->loginAs($john)
                ->visit(route('profiles.show', $john))
                ->assertMissing('@ignore-user-button');
        });
    }

}