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
        $comment = CommentFactory::create();
        $this->signIn();

        $response = $this->get(
            route('api.comments.index', $comment->profilePost)
        )->json();

        $this->assertCount(1, ($response['data']));
    }
}