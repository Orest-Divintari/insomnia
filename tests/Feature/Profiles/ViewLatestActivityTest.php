<?php

namespace Tests\Feature\Profiles;

use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewLatestActivityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function members_may_view_the_latest_activity_of_the_profile_owner()
    {
        $this->withoutExceptionHandling();
        $profileOwner = $this->signIn();
        $thread = ThreadFactory::by($profileOwner)->create();
        $profilePost = ProfilePostFactory::by($profileOwner)->create();
        $threadReply = ReplyFactory::by($profileOwner)
            ->toThread($thread)
            ->create();
        $comment = CommentFactory::by($profileOwner)
            ->toProfilePost($profilePost)
            ->create();
        $threadReply->likedBy($profileOwner);
        $comment->likedBy($profileOwner);
        $profilePost->likedBy($profileOwner);

        $activities = $this->getJson(
            route('ajax.latest-activity.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(7, $activities);
    }

    /** @test */
    public function guests_may_not_see_the_latest_activities_of_a_user()
    {
        $response = $this->getJson(
            route('ajax.latest-activity.index', create(User::class))
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}