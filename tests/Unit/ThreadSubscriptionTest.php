<?php

namespace Tests\Unit;

use App\Thread;
use App\ThreadSubscription;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = create(User::class);
        $this->thread = create(Thread::class);
        $this->thread->subscribe($this->user->id);
        $this->subscription = ThreadSubscription::Of($this->thread->id, $this->user->id);
    }

    /** @test */
    public function get_the_subscription_for_a_specific_thread_and_specific_user()
    {
        $this->assertEquals($this->thread->id, $this->subscription->thread_id);
        $this->assertEquals($this->user->id, $this->subscription->user_id);
    }

    /** @test */
    public function a_subscrition_belongs_to_a_thread()
    {
        $this->assertInstanceOf(Thread::class, $this->subscription->thread);
    }

    /** @test */
    public function a_subscrition_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->subscription->user);
    }

    /** @test */
    public function disable_email_notifications_for_a_specific_subscription()
    {
        $this->assertTrue($this->subscription->prefers_email);

        $this->subscription->disableEmailNotifications();

        $this->assertFalse($this->subscription->prefers_email);

    }

    /** @test */
    public function enable_email_notifications_for_a_specific_subscription()
    {
        $this->subscription->disableEmailNotifications();
        $this->assertFalse($this->subscription->prefers_email);

        $this->subscription->enableEmailNotifications();

        $this->assertTrue($this->subscription->prefers_email);
    }

}