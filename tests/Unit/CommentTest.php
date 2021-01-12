<?php

namespace Tests\Unit;

use App\ProfilePost;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_comment_belongs_has_a_poster()
    {
        $user = create(User::class);
        $comment = CommentFactory::by($user)->create();

        $this->assertInstanceOf(User::class, $comment->poster);
        $this->assertEquals($user->id, $comment->poster->id);
    }

    /** @test */
    public function a_comment_belongs_to_a_profile_post()
    {
        $profilePost = create(ProfilePost::class);
        $comment = CommentFactory::toProfilePost($profilePost)->create();

        $this->assertInstanceOf(ProfilePost::class, $comment->repliable);
        $this->assertEquals($profilePost->id, $comment->repliable->id);
    }

    /** @test */
    public function a_comment_has_activities()
    {
        $user = $this->signIn();

        $comment = CommentFactory::by($user)->create();

        $this->assertCount(1, $comment->activities);
    }

}