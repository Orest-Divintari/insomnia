<?php

namespace Tests\Unit;

use App\ProfilePost;
use App\Reply;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_comment_belongs_to_a_user()
    {
        $post = create(ProfilePost::class);
        $poster = $this->signIn();

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $poster->id,
            ],
            $poster
        );

        $this->assertInstanceOf(User::class, $comment->poster);
    }

    /** @test */
    public function a_comment_belongs_to_a_profile_post()
    {
        $poster = $this->signIn();
        $post = create(ProfilePost::class);

        $comment = $post->addComment(
            [
                'body' => 'some body',
                'user_id' => $poster->id,
            ],
            $poster
        );

        $this->assertInstanceOf(ProfilePost::class, $comment->profilePost);
    }

    /** @test */
    public function a_comment_has_activities()
    {
        $user = $this->signIn();
        $thread = create(ProfilePost::class);

        $comment = $thread->addComment(
            raw(Reply::class, [
                'user_id' => $user->id,
            ]),
            $user);

        $this->assertCount(1, $comment->activities);
    }

}