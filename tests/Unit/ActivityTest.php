<?php

namespace Tests\Unit;

use App\Actions\ActivityLogger;
use App\Activity;
use App\Thread;
use App\User;
use Carbon\Carbon;
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

        $this->assertInstanceOf(Thread::class, $threadActivity->subject);
        $this->assertEquals($thread->id, $threadActivity->subject->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $activities = Activity::first();

        $this->assertInstanceOf(User::class, $activities->user);
        $this->assertEquals($user->id, $activities->user->id);
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

    /** @test */
    public function it_fetches_only_the_activities_of_type_viewed()
    {
        $logger = new ActivityLogger;
        $this->signIn();
        $viewedThread = 'viewed-thread';
        $logger->type('created')->log();
        $logger->type($viewedThread)->log();

        $activities = Activity::typeViewed()->get();

        $this->assertCount(1, $activities);
        $this->assertEquals($viewedThread, $activities->first()->type);
    }

    /** @test */
    public function it_fetches_only_the_activities_of_type_created()
    {
        $this->signIn();
        $logger = new ActivityLogger;
        $logger->type('viewed')->log();
        $logger->type('created')->log();

        $activities = Activity::typeCreated()->get();
        $this->assertCount(1, $activities);
        $this->assertEquals('created', $activities->first()->type);
    }

    /** @test */
    public function it_fetches_the_activities_of_registered_users()
    {
        $logger = new ActivityLogger;
        $logger->description('by guest')->log();
        $user = $this->signIn();
        $logger->type('by registered user')->log();

        $activities = Activity::byMembers()->get();

        $this->assertCount(1, $activities);
        $this->assertEquals($user->id, $activities->first()->user->id);
    }

    /** @test */
    public function it_fetches_the_activities_of_guests()
    {
        $logger = new ActivityLogger;
        $activityByGuest = $logger->description('by guest')->log();
        $user = $this->signIn();
        $logger->type('by registered user')->log();

        $activities = Activity::byGuests()->get();

        $this->assertCount(1, $activities);
        $this->assertEquals(
            $activityByGuest->guest_id,
            $activities->first()->guest_id
        );
    }

    /** @test */
    public function it_fetches_the_activities_that_were_created_the_past_fifteen_minutes()
    {
        $logger = new ActivityLogger;
        Carbon::setTestNow(Carbon::now()->subMinutes(20));
        $user = $this->signIn();
        $oldActivity = $logger->type('something')->log();
        Carbon::setTestNow();
        $recentActivity = $logger->type('something else')->log();

        $activities = Activity::lastFifteenMinutes()->get();

        $this->assertCount(1, $activities);
        $this->assertEquals($activities->first()->id, $recentActivity->id);
    }

    /** @test */
    public function it_fetches_the_activities_that_were_created_the_past_give_number_of_minutes()
    {
        $logger = new ActivityLogger;
        $minutesAgo = 5;
        Carbon::setTestNow(Carbon::now()->subMinutes(6));
        $user = $this->signIn();
        $oldActivity = $logger->type('something')->log();
        Carbon::setTestNow();
        $recentActivity = $logger->type('something else')->log();

        $activities = Activity::lastMinutes($minutesAgo)->get();

        $this->assertCount(1, $activities);
        $this->assertEquals($activities->first()->id, $recentActivity->id);
    }

}