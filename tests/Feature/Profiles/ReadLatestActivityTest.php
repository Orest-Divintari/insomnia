<?php

namespace Tests\Feature\Profiles;

use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadLatestActivityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_read_the_latest_activity_of_the_profile_owner()
    {
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

        $activities = $this->getJson(
            route('ajax.latest-activity.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(6, $activities);
    }
}
