<?php

namespace Tests\Unit;

use App\Actions\ActivityLogger;
use App\ViewModels\OnlineUserActivitiesViewModel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineUserActivitiesViewModelTest extends TestCase
{
    use RefreshDatabase;

    protected $online;
    protected $logger;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger = new ActivityLogger;
    }

    /** @test */
    public function it_fetches_the_latest_activities_of_all_online_users()
    {
        $this->logger
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $user = $this->signIn();
        $this->logger
            ->type('viewed')
            ->description('something')
            ->by($user)
            ->log();
        $viewModel = new OnlineUserActivitiesViewModel();

        $activities = $viewModel->activities();

        $this->assertCount(2, $activities->items());
    }

    /** @test */
    public function it_fetches_the_latest_activities_of_online_guests()
    {
        $this->logger
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();

        $user = $this->signIn();
        $this->logger
            ->type('viewed')
            ->description('something')
            ->by($user)
            ->log();
        $type = 'guest';

        $viewModel = new OnlineUserActivitiesViewModel($type);

        $activities = $viewModel->activities();

        $this->assertCount(1, $activities->items());
    }

    /** @test */
    public function it_fetches_the_latest_activities_of_online_members()
    {
        $this->logger
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $user = $this->signIn();
        $this->logger
            ->type('viewed')
            ->description('something')
            ->by($user
            )->log();
        $type = 'member';
        $viewModel = new OnlineUserActivitiesViewModel($type);

        $activities = $viewModel->activities();

        $this->assertCount(1, $activities->items());
    }

    /** @test */
    public function it_fetches_only_the_activities_of_online_users_that_took_place_the_last_fifteen_minutes()
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(20));
        $this->logger
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $user = $this->signIn();
        $this->logger
            ->type('viewed')
            ->description('something')
            ->by($user)
            ->log();
        Carbon::setTestNow();
        $this->logger
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $user = $this->signIn();
        $this->logger
            ->type('viewed')
            ->description('something')
            ->by($user)
            ->log();
        $viewModel = new OnlineUserActivitiesViewModel();

        $activities = $viewModel->activities();

        $this->assertCount(2, $activities->items());
    }

    /** @test */
    public function it_fetches_the_number_of_online_registered_users_that_were_active_the_past_fifteen_minutes()
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(20));
        $this->logger
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $user = $this->signIn();
        $logger = (new ActivityLogger);
        $user = $this->signIn();
        $logger
            ->type('viewed')
            ->description('something')
            ->log();
        $viewModel = new OnlineUserActivitiesViewModel();

        $this->assertEquals(1, $viewModel->membersCount());
    }

    /** @test */
    public function it_fetches_the_number_of_online_guest_users_that_were_active_the_past_fifteen_minutes()
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(20));
        $this->logger
            ->byGuest()
            ->type('viewed')
            ->description('something')->log();
        Carbon::setTestNow();

        $this->logger
            ->byGuest()
            ->type('viewed')
            ->description('something')->log();
        $viewModel = new OnlineUserActivitiesViewModel();

        $this->assertEquals(1, $viewModel->guestsCount());
    }

    /** @test */
    public function it_fetches_the_total_number_of_online_users_who_were_active_the_past_fifteen_minutes()
    {
        Carbon::setTestNow(Carbon::now()->subMinutes(20));
        (new ActivityLogger)
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $user = $this->signIn();
        (new ActivityLogger)
            ->type('viewed')
            ->description('something')
            ->by($user)
            ->log();
        Carbon::setTestNow();
        (new ActivityLogger)
            ->type('viewed')
            ->description('something')
            ->by($user)
            ->log();
        (new ActivityLogger)
            ->type('viewed')
            ->description('something')
            ->byGuest()
            ->log();
        $viewModel = new OnlineUserActivitiesViewModel();

        $this->assertEquals(2, $viewModel->totalUsersCount());
    }
}