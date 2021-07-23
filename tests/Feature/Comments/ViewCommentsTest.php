<?php

namespace Tests\Feature\Comments;

use App\Models\ProfilePost;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_view_all_the_comments_associated_with_a_post()
    {
        $profilePost = create(ProfilePost::class);
        $oldComment = CommentFactory::toProfilePost($profilePost)->create();
        Carbon::setTestNow(Carbon::now()->addHour());
        $newComment = CommentFactory::toProfilePost($profilePost)->create();
        Carbon::setTestNow();
        $user = $this->signIn();
        $oldComment->like($user);

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

    /** @test */
    public function an_authenticated_user_may_jump_to_a_specific_comment()
    {
        $orestis = create(User::class);
        $john = $this->signIn();
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * 5);
        $lastPost = $posts->last();
        $comment = CommentFactory::by($john)->toProfilePost($lastPost)->create();

        $response = $this->get("/profile-posts/comments/$comment->id");

        $response->assertRedirect($comment->path);
    }

    /** @test */
    public function guests_may_not_jump_to_a_specific_comment()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * 5);
        $lastPost = $posts->last();
        $comment = CommentFactory::by($john)->toProfilePost($lastPost)->create();

        $response = $this->get("/profile-posts/comments/{$comment->id}");

        $response->assertRedirect('login');
    }
}
