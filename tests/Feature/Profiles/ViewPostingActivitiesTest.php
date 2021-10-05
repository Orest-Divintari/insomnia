<?php

namespace Tests\Feature\Profiles;

use App\Models\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewPostingActivitiesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function members_may_view_the_posting_activities_of_the_profile_owner()
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
        $replyLike = $threadReply->like();
        $commentLike = $comment->like();

        $postings = $this->getJson(
            route('ajax.posting-activity.index', $profileOwner)
        )->json()['data'];

        $this->assertCount(4, $postings);
    }

    /** @test */
    public function guests_may_not_view_the_posting_activities_of_a_user()
    {
        $response = $this->getJson(
            route('ajax.posting-activity.index', create(User::class))
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}