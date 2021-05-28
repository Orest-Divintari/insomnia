<?php

namespace Tests\Feature\Events;

use App\Listeners\Conversation\NotifyMessagePoster;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use \Facades\Tests\Setup\ConversationFactory;

class MessageWasLikedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_conversation_participant_likes_a_message_then_an_event_is_fired()
    {
        $listener = Mockery::spy(NotifyMessagePoster::class);
        app()->instance(NotifyMessagePoster::class, $listener);
        $conversationStarter = $this->signIn();
        $liker = create(User::class);
        $conversation = ConversationFactory::withParticipants([$liker->name])->create();
        $message = $conversation->messages->first();
        $this->signIn($liker);

        $like = $message->likedBy($liker);

        $listener->shouldHaveReceived('handle', function ($event) use (
            $like,
            $conversation,
            $message,
            $conversationStarter,
            $liker
        ) {
            return $event->like->id == $like->id
            && $event->conversation->id == $conversation->id
            && $event->messagePoster->id == $conversationStarter->id
            && $event->message->id == $message->id
            && $event->liker->id == $liker->id;
        });

    }
}