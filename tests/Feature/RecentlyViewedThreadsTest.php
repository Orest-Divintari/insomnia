<?php

namespace Tests\Feature;

use App\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentlyViewedThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function display_the_most_recently_viewed_threads_by_authenticated_user()
    {
        $user = $this->signIn();
        $recentlyViewedThread = create(Thread::class);
        $recentlyViewedThread = Thread::where('id', $recentlyViewedThread->id)
            ->withRecentReply()
            ->withCount('replies')
            ->withReadAt()
            ->first();
        $recentlyViewedThread->read($user);
        $oldThread = create(Thread::class);
        Carbon::setTestNow(Carbon::now()->subYear());
        $oldThread->read($user);
        Carbon::setTestNow();

        $response = $this->get(route('recently-viewed-threads.index'));

        $response->assertSee($recentlyViewedThread->title)
            ->assertSee($recentlyViewedThread->category->title)
            ->assertSee($recentlyViewedThread->poster->name)
            ->assertSee($recentlyViewedThread->recentReply->poster->name)
            ->assertSee($recentlyViewedThread->replies_count)
            ->assertSee($recentlyViewedThread->read_at);
    }

    /** @test */
    public function it_shows_a_message_when_a_user_has_not_viewed_any_threads_yet()
    {
        $message = 'You have not viewed any threads yet';
        $this->signIn();

        $response = $this->get(route('recently-viewed-threads.index'));

        $response->assertSee($message);
    }
}