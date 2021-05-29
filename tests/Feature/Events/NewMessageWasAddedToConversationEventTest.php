<?php

namespace Tests\Feature\Events;

use App\Listeners\Conversation\NotifyConversationParticipants;
use App\Reply;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NewMessageWasAddedToConversationEventTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function when_a_new_message_is_added_to_a_conversation_then_an_event_is_fired()
    {
        $listener = Mockery::spy(NotifyConversationParticipants::class);
        app()->instance(NotifyConversationParticipants::class, $listener);
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->post(
            route('ajax.messages.store', $conversation),
            ['body' => 'some message']
        );
        $message = Reply::whereBody('some message')->first();

        $listener->shouldHaveReceived('handle', function ($event) use (
            $conversation,
            $message
        ) {
            return $event->conversation->id == $conversation->id
            && $event->message->id == $message->id;
        });
    }
}