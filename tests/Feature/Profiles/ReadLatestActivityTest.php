<?php

namespace Tests\Feature\Profiles;

use App\ProfilePost;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadLatestActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_the_latest_activity_of_the_profile_owner()
    {
        $profileOwner = $this->signIn();
        $thread = create(Thread::class);
        $profilePost = create(ProfilePost::class);
        $threadReply = $thread->addReply(
            ['body' => 'some body', 'user_id' => $profileOwner->id]
        );
        $comment = $profilePost->addComment(
            ['body' => 'some body', 'user_id' => $profileOwner->id],
            $profileOwner
        );
        $replyLike = $threadReply->likedBy();
        $commentLike = $comment->likedBy();

        $activities = $this->getJson(
            route('api.latest-activity.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(6, $activities);
    }
}