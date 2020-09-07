<?php

namespace Tests\Feature;

use App\Activity;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadLatestActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_the_latest_activity_of_another_user()
    {
        $profileOwner = $this->signIn();

        $numberOfActivities = Activity::NUMBER_OF_ACTIVITIES;

        createMany(Thread::class, $numberOfActivities, ['user_id' => $profileOwner->id]);

        $this->assertCount($numberOfActivities, $profileOwner->activities);

        $user = $this->signIn();

        $response = $this->get(route('api.latest-activity.index', $profileOwner))->json();

        $this->assertCount($numberOfActivities, $response['data']);
    }
}