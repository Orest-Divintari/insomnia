<?php

namespace Tests\Feature\Event;

use App\Listeners\Like\DeletePostLikeNotification;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PostWasUnlikedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_unlikes_a_thread_reply_an_event_is_fired()
    {
        $listener = Mockery::spy(DeletePostLikeNotification::class);
        app()->instance(DeletePostLikeNotification::class, $listener);
        $reply = ReplyFactory::create();
        $user = $this->signIn();
        $like = $reply->likedBy($user);

        $this->deleteJson(route('ajax.reply-likes.destroy', $reply));

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($reply, $like) {
                return $event->post->is($reply) &&
                $event->likeId == $like->id;
            }));
    }

    /** @test */
    public function when_a_user_unlikes_a_profile_post_comment_an_event_is_fired()
    {
        $listener = Mockery::spy(DeletePostLikeNotification::class);
        app()->instance(DeletePostLikeNotification::class, $listener);
        $comment = CommentFactory::create();
        $user = $this->signIn();
        $like = $comment->likedBy($user);

        $this->deleteJson(route('ajax.reply-likes.destroy', $comment));

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($comment, $like) {
                return $event->post->is($comment) &&
                $event->likeId == $like->id;
            }));
    }

    /** @test */
    public function when_a_conversation_participant_unlikes_a_conversation_message_an_event_is_fired()
    {
        $listener = Mockery::spy(DeletePostLikeNotification::class);
        app()->instance(DeletePostLikeNotification::class, $listener);
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();
        $like = $message->likedBy($participant);
        $this->signIn($participant);

        $this->deleteJson(route('ajax.reply-likes.destroy', $message));

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($message, $like) {
                return $event->post->is($message) &&
                $event->likeId == $like->id;
            }));
    }

    /** @test */
    public function when_user_unlikes_a_profile_post_then_an_event_is_fired()
    {
        $listener = Mockery::spy(DeletePostLikeNotification::class);
        app()->instance(DeletePostLikeNotification::class, $listener);
        $profilePost = ProfilePostFactory::create();
        $user = $this->signIn();
        $like = $profilePost->likedBy($user);

        $this->deleteJson(route('ajax.profile-post-likes.destroy', $profilePost));

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($profilePost, $like) {
                return $event->post->is($profilePost) &&
                $event->likeId == $like->id;
            }));
    }
}