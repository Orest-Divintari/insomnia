<?php

namespace Tests\Unit;

use App\Like;
use App\Reply;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_like_belongs_to_a_reply()
    {
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $like = Like::create([
            'user_id' => 1,
            'reply_id' => $reply->id,
        ]);

        $this->assertInstanceOf(Reply::class, $like->reply);

    }

    /** @test */
    public function a_like_has_activity()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply(
            raw(Reply::class, [
                'user_id' => $user->id,
            ]));

        $like = $reply->likedBy($user);

        $this->assertCount(1, $like->activities);

    }
}