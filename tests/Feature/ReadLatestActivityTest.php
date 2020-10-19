<?php

namespace Tests\Feature;

use App\Activity;
use App\Reply;
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

        createMany(
            Thread::class,
            $numberOfActivities,
            ['user_id' => $profileOwner->id]
        );

        $this->assertCount(
            $numberOfActivities,
            $profileOwner->activities
        );

        $user = $this->signIn();

        $response = $this->get(
            route('api.latest-activity.index', $profileOwner)
        )->json();

        $this->assertCount(
            $numberOfActivities,
            $response['data']
        );
    }

    /** @test */
    public function a_user_can_read_only_the_posting_activities()
    {
        $profileOwner = $this->signIn();
        $numberOfPostingActivities = 5;
        createMany(
            Thread::class,
            $numberOfPostingActivities,
            ['user_id' => $profileOwner->id]
        );

        $thread = Thread::first();
        $reply = $thread->addReply(
            raw(Reply::class,
                ['user_id' => $profileOwner->id])
        );
        $numberOfPostingActivities++;
        $reply->likedBy($profileOwner);

        $response = $this->get(
            route(
                'api.latest-activity.index', [
                    $profileOwner, 'postings' => true]
            ))->json();

        $this->assertCount(
            $numberOfPostingActivities,
            $response['data']
        );
    }
}