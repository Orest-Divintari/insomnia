<?php

namespace Tests\Feature;

use App\Helpers\ModelType;
use App\ProfilePost;
use App\Reply;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_like_a_reply()
    {
        $reply = ReplyFactory::create();

        $this->post(route('ajax.reply-likes.store', $reply))
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_like_a_thread_reply()
    {
        $user = $this->signIn();

        $reply = ReplyFactory::create();

        $this->post(route('ajax.reply-likes.store', $reply));

        $this->assertDatabaseHas('likes', [
            'likeable_id' => $reply->id,
            'likeable_type' => Reply::class,
            'liker_id' => $user->id,
            'type' => ModelType::like($reply),
        ]);

        $this->assertCount(1, $reply->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_like_a_profile_post_comment()
    {
        $user = $this->signIn();

        $comment = CommentFactory::create();

        $this->post(route('ajax.reply-likes.store', $comment));

        $this->assertDatabaseHas('likes', [
            'likeable_id' => $comment->id,
            'likeable_type' => Reply::class,
            'liker_id' => $user->id,
            'type' => ModelType::like($comment),
        ]);

        $this->assertCount(1, $comment->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_like_a_conversation_message()
    {
        $user = $this->signIn();
        $participant =

        $conversation = ConversationFactory::by($user)->create();
        $message = $conversation->messages()->first();

        $this->post(route('ajax.reply-likes.store', $message));

        $this->assertDatabaseHas('likes', [
            'likeable_id' => $message->id,
            'likeable_type' => Reply::class,
            'liker_id' => $user->id,
            'type' => ModelType::like($message),
        ]);

        $this->assertCount(1, $message->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_like_a_profile_post()
    {
        $user = $this->signIn();
        $profilePost = ProfilePostFactory::create();

        $this->postJson(route('ajax.profile-post-likes.store', $profilePost));

        $this->assertDatabaseHas('likes', [
            'likeable_id' => $profilePost->id,
            'likeable_type' => ProfilePost::class,
            'liker_id' => $user->id,
            'type' => ModelType::like($profilePost),
        ]);
        $this->assertCount(1, $profilePost->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_unlike_a_profile_post()
    {
        $user = $this->signIn();
        $profilePost = ProfilePostFactory::create();
        $profilePost->likedBy($user);
        $this->assertCount(1, $profilePost->likes);

        $this->deleteJson(route('ajax.profile-post-likes.destroy', $profilePost));

        $this->assertCount(0, $profilePost->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_like_a_reply_only_once()
    {
        $user = $this->signIn();
        $reply = ReplyFactory::create();
        $this->post(route('ajax.reply-likes.store', $reply));
        $this->assertCount(1, $reply->fresh()->likes);

        $this->post(route('ajax.reply-likes.store', $reply));

        $this->assertCount(1, $reply->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_like_a_profile_post_only_once()
    {
        $user = $this->signIn();
        $profilePost = ProfilePostFactory::create();
        $this->post(route('ajax.profile-post-likes.store', $profilePost));
        $this->assertCount(1, $profilePost->fresh()->likes);

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        $this->assertCount(1, $profilePost->fresh()->likes);
    }

    /** @test */
    public function an_authenticated_user_can_unlike_a_reply()
    {
        $user = $this->signIn();
        $reply = ReplyFactory::create();
        $this->post(route('ajax.reply-likes.store', $reply));
        $this->assertDatabaseHas('likes', [
            'likeable_id' => $reply->id,
            'likeable_type' => Reply::class,
            'liker_id' => $user->id,
        ]);
        $this->assertCount(1, $reply->fresh()->likes);

        $this->delete(route('ajax.reply-likes.destroy', $reply));

        $this->assertDatabaseMissing('likes', [
            'likeable_id' => $reply->id,
            'likeable_type' => Reply::class,
            'liker_id' => $user->id,
        ]);
        $this->assertCount(0, $reply->fresh()->likes);
    }

}