<?php

namespace Tests\Unit;

use App\Actions\ActivityLogger;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggerTest extends TestCase
{
    use RefreshDatabase;

    protected $logger;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger = new ActivityLogger;
    }

    /** @test */
    public function it_logs_an_activity_with_a_subject()
    {
        $thread = create(Thread::class);

        $activity = $this->logger->on($thread)->log();

        $this->assertEquals($activity->subject->id, $thread->id);
        $this->assertInstanceOf(Thread::class, $activity->subject);
    }

    /** @test */
    public function it_logs_an_activity_with_type()
    {
        $type = 'viewed';

        $activity = $this->logger->type($type)->log();

        $this->assertEquals($activity->type, $type);
    }

    /** @test */
    public function it_logs_an_activity_with_description()
    {
        $this->logger = new ActivityLogger;
        $description = 'viewed something';

        $activity = $this->logger->description($description)->log();

        $this->assertEquals($activity->description, $description);
    }

    /** @test */
    public function it_logs_an_activity_using_a_given_user_as_the_causer()
    {
        $user = $this->signIn();

        $activity = $this->logger->by($user)->log();

        $this->assertEquals($activity->user->id, $user->id);
        $this->assertInstanceOf(User::class, $activity->user);
    }

    /** @test */
    public function it_logs_an_activity_using_a_guest_user_as_the_causer()
    {
        $activity = $this->logger->byGuest()->log();

        $this->assertNull($activity->user_id);
        $this->assertNotNull($activity->guest_id);
    }

    /** @test */
    public function when_the_causer_is_not_specified_and_there_is_an_authenticated_user_then_it_will_use_the_authenticated_user_as_default_causer()
    {
        $user = $this->signIn();

        $activity = $this->logger->description('viewed something')->log();

        $this->assertEquals($activity->user->id, $user->id);
    }

    /** @test */
    public function when_the_causer_is_not_specified_and_there_is_no_authenticated_user_then_it_will_use_the_session_token_as_the_default_causer_id()
    {
        $activity = $this->logger->description('viewed something')->log();

        $this->assertNotNull($activity->guest_id);
    }

}