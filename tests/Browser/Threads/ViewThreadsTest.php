<?php

namespace Tests\Browser\Threads;

use App\Category;
use App\Thread;
use App\User;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewThreadsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_view_the_pagination_buttons_of_the_last_three_pages_of_replies_of_a_thread_of_a_given_category()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $numberOfPages = 10;
        $threadBody = 1;
        $numberOfReplies = Thread::REPLIES_PER_PAGE * $numberOfPages - $threadBody;
        $thread->increment('replies_count', $numberOfReplies);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit(route('category-threads.index', $category))
                ->assertSeeLink('8')
                ->assertSeeLink('9')
                ->assertSeeLink('10');
        });
    }

    /** @test */
    public function the_authenticated_user_should_not_see_the_replies_created_by_ignored_users()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $john->ignore($doe);
        $ignoredReply = ReplyFactory::by($doe)->toThread($thread)->create();

        $this->browse(function (Browser $browser) use ($thread, $ignoredReply, $john) {
            $browser->loginAs($john)
                ->visit(route('threads.show', $thread))
                ->assertDontSee($ignoredReply->body)
                ->assertVisible('@show-ignored-content-button');
        });
    }

    /** @test */
    public function the_authenticated_user_can_reveal_the_ignored_replies()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $john->ignore($doe);
        $ignoredReply = ReplyFactory::by($doe)->toThread($thread)->create();

        $this->browse(function (Browser $browser) use ($thread, $ignoredReply, $john) {
            $browser->loginAs($john)
                ->visit(route('threads.show', $thread))
                ->assertDontSee($ignoredReply->body)
                ->click('@show-ignored-content-button')
                ->assertSee($ignoredReply->body)
                ->assertSee('You are ignoring content by this member.');
        });
    }

    /** @test */
    public function the_authenticated_thread_poster_should_not_see_the_ignore_button()
    {
        $category = create(Category::class);
        $john = create(User::class);
        $thread = ThreadFactory::inCategory($category)->by($john)->create();

        $this->browse(function (Browser $browser) use ($thread, $john) {
            $browser->loginAs($john)
                ->visit(route('threads.show', $thread))
                ->assertMissing('@ignore-thread-button');
        });
    }

    /** @test */
    public function guests_should_not_see_the_input_to_post_new_reply()
    {
        $thread = create(Thread::class);

        $this->browse(function (Browser $browser) use ($thread) {
            $response = $browser->visit(route('threads.show', $thread));

            $response->assertMissing('@new-reply-input');
        });
    }

    /** @test */
    public function guests_should_not_see_the_watch_button()
    {
        $thread = create(Thread::class);

        $this->browse(function (Browser $browser) use ($thread) {
            $response = $browser->visit(route('threads.show', $thread));

            $response->assertMissing('@subscribe-thread-button');
        });
    }

    /** @test */
    public function guests_should_not_see_the_like_button()
    {
        $thread = create(Thread::class);

        $this->browse(function (Browser $browser) use ($thread) {
            $response = $browser->visit(route('threads.show', $thread));

            $response->assertMissing('@like-button');
        });
    }

    /** @test */
    public function members_should_see_the_watch_button()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@subscribe-thread-button');
        });
    }

    /** @test */
    public function members_should_not_see_the_pin_thread_button()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertMissing('@pin-thread-button');
        });
    }

    /** @test */
    public function guests_should_not_see_the_pin_thread_button()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->visit(route('threads.show', $thread));

            $response->assertMissing('@pin-thread-button');
        });
    }

    /** @test */
    public function members_should_not_see_the_lock_thread_button()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertMissing('@lock-thread-button');
        });
    }

    /** @test */
    public function guests_should_not_see_the_lock_thread_button()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->visit(route('threads.show', $thread));

            $response->assertMissing('@lock-thread-button');
        });
    }

    /** @test */
    public function authorised_users_may_see_the_ignore_button()
    {
        $category = create(Category::class);
        $john = create(User::class);
        $doe = create(User::class);
        $thread = ThreadFactory::inCategory($category)->by($doe)->create();

        $this->browse(function (Browser $browser) use ($thread, $john) {
            $browser->loginAs($john)
                ->visit(route('threads.show', $thread))
                ->assertVisible('@ignore-thread-button');
        });
    }

}