<?php

namespace Tests\Unit;

use App\Activity;
use App\ProfilePost;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fetch_the_feed_for_a_user()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $profilePost = create(ProfilePost::class);
        $threadReply = $thread->addReply(
            ['body' => 'some body', 'user_id' => $user->id]
        );
        $comment = $profilePost->addComment(
            ['body' => 'some body', 'user_id' => $user->id],
            $user
        );
        $replyLike = $threadReply->likedBy();
        $commentLike = $comment->likedBy();

        $activities = Activity::feed($user)->get();

        $this->assertCount(6, $activities);
    }

    /** @test */
    public function fetch_only_the_feed_posts()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $profilePost = create(ProfilePost::class);
        $threadReply = $thread->addReply(
            ['body' => 'some body', 'user_id' => $user->id]
        );
        $comment = $profilePost->addComment(
            ['body' => 'some body', 'user_id' => $user->id],
            $user
        );
        $replyLike = $threadReply->likedBy();
        $commentLike = $comment->likedBy();

        $activities = Activity::feedPosts($user)->get();

        $this->assertCount(4, $activities);
    }
}