<?php

namespace Tests\Feature;

use App\ProfilePost;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_all_the_comments_associated_with_a_post()
    {
        $comment = CommentFactory::create();

        $this->signIn();

        $response = $this->get(route('api.comments.index', $comment->profilePost))->json();

        $this->assertCount(1, ($response['data']));

    }
}