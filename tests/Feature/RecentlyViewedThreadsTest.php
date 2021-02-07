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
        $oldThread = create(Thread::class);
        $user->read($recentlyViewedThread);
        Carbon::setTestNow(Carbon::now()->subYear());
        $user->read($oldThread);
        Carbon::setTestNow();

        $response = $this->get(route('recently-viewed-threads.index'));

        $response->assertSee($recentlyViewedThread->title);
    }
}