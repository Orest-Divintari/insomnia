<?php

namespace Tests\Unit;

use App\Activity;
use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_fetch_the_thread_activity()
    {
        $user = $this->signIn();

        $thread = create(Thread::class, [
            'user_id' => $user->id,
        ]);

        $reply = $thread->addReply(raw(Reply::class, ['user_id' => $user->id]));

        $reply->likedBy($user);

        $activity = Activity::feed($user)->paginate(Activity::NUMBER_OF_ACTIVITIES);

        $this->assertCount(3, $activity);

    }
}