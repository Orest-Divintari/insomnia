<?php

namespace Tests\Browser\Threads;

use App\Thread;
use App\User;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ThreadInteractionTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function admins_may_lock_a_thread()
    {
        $thread = create(Thread::class);
        $admin = createAdminUser();

        $this->browse(function (Browser $browser) use ($thread, $admin) {
            $response = $browser->loginAs($admin)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@lock-thread-button')
                ->assertSee('Lock')
                ->click('@lock-thread-button')
                ->pause(500)
                ->assertSee('Unlock')
                ->assertSee('Closed for new replies.')
                ->assertMissing('@new-reply-input')
                ->refresh()
                ->assertSee('Unlock')
                ->assertSee('Closed for new replies.')
                ->assertMissing('@new-reply-input');
        });
    }

    /** @test */
    public function admins_may_unlock_a_thread()
    {
        $thread = create(Thread::class);
        $admin = createAdminUser();
        $thread->lock();

        $this->browse(function (Browser $browser) use ($thread, $admin) {
            $response = $browser->loginAs($admin)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@lock-thread-button')
                ->assertSee('Unlock')
                ->assertSee('Closed for new replies.')
                ->click('@lock-thread-button')
                ->waitForText('Lock')
                ->assertSee('Lock')
                ->assertVisible('@new-reply-input')
                ->refresh()
                ->assertSee('Lock')
                ->assertVisible('@new-reply-input');
        });
    }

    /** @test */
    public function admins_may_pin_a_thread()
    {
        $thread = create(Thread::class);
        $admin = createAdminUser();

        $this->browse(function (Browser $browser) use ($thread, $admin) {
            $response = $browser->loginAs($admin)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@pin-thread-button')
                ->assertSee('Pin')
                ->click('@pin-thread-button')
                ->pause(300)
                ->assertSee('Unpin');

            $browser->visit(route('threads.index', $thread->category))
                ->assertSee('Sticky Threads');
        });
    }

    /** @test */
    public function admins_may_unpin_a_thread()
    {
        $thread = create(Thread::class);
        $admin = createAdminUser();
        $thread->pin();

        $this->browse(function (Browser $browser) use ($thread, $admin) {
            $response = $browser->loginAs($admin)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@pin-thread-button')
                ->assertSee('Unpin')
                ->click('@pin-thread-button');

            $browser->visit(route('threads.index', $thread->category))
                ->assertMissing('Sticky Threads');
        });
    }

    /** @test */
    public function members_may_watch_a_thread_and_receive_email_notifications()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@subscribe-thread-button')
                ->assertSee('Watch')
                ->click('@subscribe-thread-button')
                ->assertVisible('@watch-thread-modal')
                ->assertChecked('@with-email-notifications-radio-button')
                ->click('@modal-watch-button')
                ->waitForText('Unwatch')
                ->assertSee('Unwatch')
                ->refresh()
                ->assertSee('Unwatch');
        });
    }

    /** @test */
    public function members_may_watch_a_thread_and_without_receiving_email_notifications()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@subscribe-thread-button')
                ->assertSee('Watch')
                ->click('@subscribe-thread-button')
                ->assertVisible('@watch-thread-modal')
                ->check('@without-email-notifications-radio-button')
                ->assertChecked('@without-email-notifications-radio-button')
                ->click('@modal-watch-button')
                ->waitForText('Unwatch')
                ->assertSee('Unwatch')
                ->refresh()
                ->assertSee('Unwatch');
        });
    }

    /** @test */
    public function members_may_unwatch_a_thread()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $thread->subscribe($user->id);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertVisible('@subscribe-thread-button')
                ->assertSee('Unwatch')
                ->click('@subscribe-thread-button')
                ->assertVisible('@unwatch-thread-modal')
                ->assertSee('Are you sure you want to unwatch this thread ?')
                ->click('@modal-unwatch-button')
                ->assertNotPresent('unwatch-thread-modal')
                ->waitForText('Watch')
                ->assertSee('Watch')
                ->refresh()
                ->assertSee('Watch');
        });
    }

    /** @test */
    public function members_may_like_a_reply()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response
                ->click('@like-button')
                ->waitForText('1 likes')
                ->assertSee('1 likes')
                ->refresh()
                ->assertSee('1 likes');
        });
    }

    /** @test */
    public function members_may_unlike_a_reply()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $thread->replies()->first()->like($user);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser
                ->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response
                ->assertSee('1 likes')
                ->click('@like-button')
                ->waitUntilMissingText('1 likes')
                ->assertDontSee('1 likes')
                ->refresh()
                ->assertDontSee('1 likes');
        });
    }

    /** @test */
    public function members_may_post_a_reply()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $newReply = $this->faker()->sentence();

        $this->browse(function (Browser $browser) use ($thread, $user, $newReply) {
            $response = $browser
                ->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response->assertPresent('@new-reply-input')
                ->type('.ql-editor', $newReply)
                ->click('@post-reply-button')
                ->pause(200)
                ->assertSee($newReply);
        });
    }

    /** @test */
    public function members_may_click_on_poster_avatar_or_username_to_visit_profile()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $response = $browser
                ->loginAs($user)
                ->visit(route('threads.show', $thread));

            $response
                ->click('@profile-popover-trigger')
                ->assertPresent('@profile-component');
        });
    }

    /** @test */
    public function users_may_view_threads_of_the_current_category()
    {
        $thread = create(Thread::class);
        $category = $thread->category;
        $this->browse(function (Browser $browser) use ($thread, $category) {
            $response = $browser
                ->visit(route('threads.show', $thread));

            $response
                ->clickLink($category->title)
                ->assertRouteIs('category-threads.index', $category);
        });
    }

    /** @test */
    public function users_may_go_to_forum_home_page()
    {
        $thread = create(Thread::class);
        $this->browse(function (Browser $browser) use ($thread) {
            $response = $browser
                ->visit(route('threads.show', $thread));

            $response
                ->clickLink('Forum')
                ->assertRouteIs('forum');
        });
    }

    /** @test */
    public function users_may_sort_replies_by_number_of_likes()
    {
        $thread = create(Thread::class);
        $reply = ReplyFactory::toThread($thread)->create();
        $user = create(User::class);
        $reply->like($user);

        $this->browse(function (Browser $browser) use ($thread) {
            $response = $browser
                ->visit(route('threads.show', $thread));

            $response
                ->click('@sort-by-likes-link')
                ->assertQueryStringHas('sort_by_likes', 1);
        });
    }

    /** @test */
    public function users_may_sort_replies_by_date()
    {
        $thread = create(Thread::class);
        $reply = ReplyFactory::toThread($thread)->create();
        $user = create(User::class);
        $reply->like($user);

        $this->browse(function (Browser $browser) use ($thread) {
            $response = $browser
                ->visit(route('threads.show', $thread));

            $response
                ->click('@sort-by-likes-link')
                ->assertQueryStringHas('sort_by_likes', 1)
                ->click('@sort-by-date-link')
                ->assertQueryStringMissing('sort_by_likes');
        });
    }

    /** @test */
    public function users_may_jump_to_the_newest_reply()
    {
        $thread = create(Thread::class);
        $numberOfPages = 2;
        $expectedPageNumber = 3;
        $replies = ReplyFactory::toThread($thread)->createMany(Thread::REPLIES_PER_PAGE * $numberOfPages);
        $lastReply = $replies->last();
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($thread, $lastReply, $expectedPageNumber) {
            $response = $browser
                ->visit(route('threads.show', $thread))
                ->click('@jump-to-new-button');

            $response
                ->assertQueryStringHas('page', $expectedPageNumber)
                ->assertFragmentIs('post-' . $lastReply->id)
                ->assertSee($lastReply->body);
        });
    }

}