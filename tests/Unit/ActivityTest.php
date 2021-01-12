<?php

namespace Tests\Unit;

use App\Activity;
use App\Thread;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fetch_the_feed_for_a_user()
    {
        $user = $this->signIn();
        $this->createActivitiesFor($user);

        $activities = Activity::feed($user)->get();

        $this->assertCount(6, $activities);
    }

    /** @test */
    public function fetch_only_the_feed_posts()
    {
        $user = $this->signIn();
        $this->createActivitiesFor($user);

        $activities = Activity::feedPosts($user)->get();

        $this->assertCount(4, $activities);
    }

    /** @test */
    public function it_has_a_subject()
    {
        $this->signIn();
        $thread = create(Thread::class);

        $threadActivity = $thread->activities->first();

        $this->assertInstanceOf(
            Thread::class,
            $threadActivity->subject
        );
    }

    public function createActivitiesFor($user)
    {
        $thread = ThreadFactory::by($user)->create();
        $profilePost = ProfilePostFactory::by($user)->create();
        $threadReply = ReplyFactory::by($user)
            ->toThread($thread)
            ->create();
        $comment = CommentFactory::by($user)
            ->toProfilePost($profilePost)
            ->create();
        $replyLike = $threadReply->likedBy();
        $commentLike = $comment->likedBy();
    }

}