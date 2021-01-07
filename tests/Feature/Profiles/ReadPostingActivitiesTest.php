<?php

namespace Tests\Feature\Profiles;

use App\ProfilePost;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadProfilePostingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_the_posting_activities_of_the_profile_owner()
    {
        $this->withExceptionHandling();
        $profileOwner = $this->signIn();

        $thread = create(Thread::class);
        $profilePost = create(ProfilePost::class);
        $threadReply = $thread->addReply(
            ['body' => 'some body', 'user_id' => $profileOwner->id]
        );
        $comment = $profilePost->addComment('some body', $profileOwner);

        $replyLike = $threadReply->likedBy();
        $commentLike = $comment->likedBy();

        $postings = $this->getJson(
            route('api.posting-activity.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(4, $postings);
    }
}