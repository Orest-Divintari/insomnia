<?php

namespace Tests\Feature\Likes;

use App\Like;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteLikesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_reply_is_deleted_all_the_associated_likes_are_deleted()
    {
        $user = $this->signIn();
        $reply = ReplyFactory::by($user)->create();
        $reply->likedBy($user);
        $this->assertCount(1, $reply->likes);

        $this->delete(route('ajax.replies.destroy', $reply));

        $this->assertEquals(0, Like::all()->count());
    }

    /** @test */
    public function when_a_profile_post_comment_is_deleted_then_all_the_associated_likes_are_deleted()
    {
        $user = $this->signIn();
        $reply = CommentFactory::by($user)->create();
        $reply->likedBy($user);
        $this->assertCount(1, $reply->likes);

        $this->delete(route('ajax.comments.destroy', $reply));

        $this->assertEquals(0, Like::all()->count());
    }

    /** @test */
    public function when_a_profile_post_is_deleted_all_the_associated_likes_are_deleted()
    {
        $profilePost = ProfilePostFactory::create();
        $profileOwner = $profilePost->profileOwner;
        $this->signIn($profileOwner);
        $profilePost->likedBy($profileOwner);
        $this->assertCount(1, $profilePost->likes);

        $profilePost->delete();

        $this->assertCount(0, Like::all());
    }
}