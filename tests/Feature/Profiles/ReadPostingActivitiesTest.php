<?php

namespace Tests\Feature\Profiles;

use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadProfilePostingsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_read_the_posting_activities_of_the_profile_owner()
    {
        $profileOwner = $this->signIn();
        $thread = ThreadFactory::by($profileOwner)->create();
        $profilePost = ProfilePostFactory::by($profileOwner)->create();
        $threadReply = ReplyFactory::toThread($thread)
            ->by($profileOwner)
            ->create();
        $comment = CommentFactory::toProfilePost($profilePost)
            ->by($profileOwner)
            ->create();
        $replyLike = $threadReply->likedBy();
        $commentLike = $comment->likedBy();

        $postings = $this->getJson(
            route('ajax.posting-activity.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(4, $postings);
    }
}
