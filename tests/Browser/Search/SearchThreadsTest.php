<?php

namespace Tests\Browser\Search;

use App\Thread;
use App\User;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchThreadsTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function the_authenticated_user_should_not_see_the_ignored_threads()
    {
        $searchTerm = $this->faker()->sentence();
        $ignoredThreadTitle = $searchTerm . ' ignored';
        $thread = ThreadFactory::withTitle($searchTerm)->create();
        $ignoredThread = ThreadFactory::withTitle($ignoredThreadTitle)->create();
        $john = create(User::class);
        $john->ignore($ignoredThread);

        $this->browse(function (Browser $browser) use ($thread, $ignoredThread, $john, $searchTerm) {
            $response = $browser
                ->loginAs($john)
                ->pause(2500)
                ->visit(route('search.index', ['type' => 'thread', 'q' => $searchTerm]));

            $response->assertSee($thread->title)
                ->assertDontSee($ignoredThread->title)
                ->assertVisible('@show-ignored-content-button');
        });
    }

    /** @test */
    public function the_authenticated_user_should_not_see_the_threads_that_are_created_by_ignored_users()
    {
        $searchTerm = $this->faker()->sentence();
        $ignoredThreadTitle = $searchTerm . ' ignored';
        $thread = ThreadFactory::withTitle($searchTerm)->create();
        $doe = create(User::class);
        $ignoredThread = ThreadFactory::withTitle($ignoredThreadTitle)->by($doe)->create();
        $john = create(User::class);
        $john->ignore($doe);

        $this->browse(function (Browser $browser) use ($thread, $ignoredThread, $john, $searchTerm) {
            $response = $browser
                ->loginAs($john)
                ->pause(2500)
                ->visit(route('search.index', ['type' => 'thread', 'q' => $searchTerm]));

            $response->assertSee($thread->title)
                ->assertDontSee($ignoredThread->title)
                ->assertVisible('@show-ignored-content-button');
        });
    }

    /** @test */
    public function the_authenticated_user_can_reveal_the_ignored_threads()
    {
        $searchTerm = $this->faker()->sentence();
        $ignoredThreadTitle = $searchTerm . ' ignored';
        $thread = ThreadFactory::withTitle($searchTerm)->create();
        $ignoredThread = ThreadFactory::withTitle($ignoredThreadTitle)->create();
        $john = create(User::class);
        $john->ignore($ignoredThread);

        $this->browse(function (Browser $browser) use ($thread, $ignoredThread, $john, $searchTerm) {
            $response = $browser
                ->loginAs($john)
                ->pause(2500)
                ->visit(route('search.index', ['type' => 'thread', 'q' => $searchTerm]));

            $response->assertSee($thread->title)
                ->assertDontSee($ignoredThread->title)
                ->click('@show-ignored-content-button')
                ->assertSee($ignoredThread->title);
        });
    }

    /** @test */
    public function the_authenticated_user_should_not_see_the_thread_replies_that_are_created_by_ignored_users()
    {
        $searchTerm = $this->faker()->sentence();
        $ignoredReplyBody = $searchTerm . ' ignored';
        $thread = create(Thread::class);
        $john = create(User::class);
        $doe = create(User::class);
        $ignoredReply = ReplyFactory::toThread($thread)->by($doe)->create();
        $john->ignore($doe);

        $this->browse(function (Browser $browser) use ($ignoredReply, $john, $searchTerm) {
            $response = $browser
                ->loginAs($john)
                ->pause(7000)
                ->visit(route('search.index', ['type' => 'thread', 'q' => $ignoredReply->body]));

            $response->assertDontSee($ignoredReply->repliable->title)
                ->assertVisible('@show-ignored-content-button');
        });
    }

}