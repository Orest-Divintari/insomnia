<?php

namespace Tests\Unit;

use App\Http\Middleware\ThrottlePosts;
use App\Models\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThrottlePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_knows_if_it_is_too_soon_to_allow_a_new_post()
    {
        $throttlePosts = new ThrottlePosts();

        $recentPost = create(Thread::class);

        $this->assertTrue($throttlePosts->tooSoonToPost($recentPost));

        $oldPost = ThreadFactory::createdAt(Carbon::now()->subDay())->create();

        $this->assertFalse($throttlePosts->tooSoonToPost($oldPost));
    }

    /** @test */
    public function it_knows_how_many_seconds_are_left_before_allowing_a_new_post()
    {
        $throttlePosts = new ThrottlePosts();

        $post = create(Thread::class);

        $secondsLeft = $post->created_at
            ->diffInSeconds(Carbon::now()->subSeconds($throttlePosts->getTimeFrame()));

        $this->assertEquals($secondsLeft, $throttlePosts->secondsLeftBeforePosting($post));
    }
}
