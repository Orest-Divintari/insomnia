<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use \Facades\Tests\Setup\ThreadFactory;

class IgnoreButtonTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_should_not_see_the_ignore_thread_button()
    {
        $thread = create(Thread::class);

        $this->browse(function (Browser $browser) use ($thread) {
            $browser
                ->visit(route('threads.show', $thread))
                ->waitForText($thread->title)
                ->assertMissing('@ignore-thread-button');
        });
    }

    /** @test */
    public function guests_should_not_see_the_ignore_user_button()
    {
        $thread = create(Thread::class);

        $this->browse(function (Browser $browser) use ($thread) {
            $browser
                ->visit(route('threads.show', $thread))
                ->waitForText($thread->title)
                ->mouseover("@{$thread->poster->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertMissing('@ignore-user-button');
        });
    }

    /** @test */
    public function authorised_users_may_ignore_a_thread()
    {
        $category = create(Category::class);
        $john = create(User::class);
        $thread = ThreadFactory::inCategory($category)->by($john)->create();
        $doe = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $doe) {
            $browser
                ->loginAs($doe)
                ->visit(route('threads.show', $thread))
                ->waitForText($thread->title)
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
        $doe->ignore($thread);

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
        $john->ignore($doe);

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

    /** @test */
    public function unverified_users_should_not_see_the_ignore_user_button()
    {
        $doe = User::factory()->unverified()->create();
        $john = create(User::class, ['name' => 'john']);
        ThreadFactory::by($john)->create();

        $this->browse(function (Browser $browser) use ($john, $doe) {
            $browser->loginAs($doe)
                ->visit(route('forum'))
                ->mouseover("@{$john->name}-profile-popover")
                ->waitFor('@profile-popover-content')
                ->assertDontSee('Ignore');
        });
    }

}