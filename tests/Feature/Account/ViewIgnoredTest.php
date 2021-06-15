<?php

namespace Tests\Feature\Account;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewIgnoredTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_ignored_users()
    {
        $ignoredUser = create(User::class);
        $user = $this->signIn();
        $ignoredUser->markAsIgnored($user);

        $response = $this->get(route('account.ignored-users.index'));

        $response->assertSee($ignoredUser->name);
    }

    /** @test */
    public function it_shows_the_ignored_threads()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $thread->markAsIgnored($user);

        $response = $this->get(route('account.ignored-threads.index'));

        $response->assertSee($thread->title);
    }
}