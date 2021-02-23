<?php

namespace Tests\Feature\Comments;

use App\ProfilePost;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_all_the_comments_associated_with_a_post()
    {
        $profilePost = create(ProfilePost::class);
        $oldComment = CommentFactory::toProfilePost($profilePost)->create();
        Carbon::setTestNow(Carbon::now()->addHour());
        $newComment = CommentFactory::toProfilePost($profilePost)->create();
        Carbon::setTestNow();
        $user = $this->signIn();
        $oldComment->likedBy($user);

        $response = $this->get(
            route('ajax.comments.index', $profilePost)
        )->json()['data'];

        $firstComment = $response[0];
        $secondComment = $response[1];
        $this->assertCount(2, $response);

        $this->assertEquals($newComment->id, $firstComment['id']);
        $this->assertFalse($firstComment['is_liked']);
        $this->assertEquals(0, $firstComment['likes_count']);
        $this->assertEquals($oldComment->id, $secondComment['id']);
        $this->assertTrue($secondComment['is_liked']);
        $this->assertEquals(1, $secondComment['likes_count']);
    }
}