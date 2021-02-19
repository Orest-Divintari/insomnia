<?php

namespace Tests\Feature\Comments;

use App\ProfilePost;
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
        $comment = CommentFactory::toProfilePost($profilePost)->create();

        $this->signIn();

        $response = $this->get(
            route('ajax.comments.index', $profilePost)
        )->json();

        $this->assertCount(1, ($response['data']));
    }
}
