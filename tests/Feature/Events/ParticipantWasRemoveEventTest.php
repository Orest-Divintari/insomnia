<?php

namespace Tests\Feature\Conversations;

use App\Listeners\Conversation\DeleteConversationReadRecord;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ParticipantWasRemoveEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_conversation_participant_is_removed_from_the_conversation_then_an_event_is_fired()
    {
        $listener = Mockery::spy(DeleteConversationReadRecord::class);
        app()->instance(DeleteConversationReadRecord::class, $listener);
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->delete(route(
            'ajax.conversation-participants.destroy',
            [
                'conversation' => $conversation->slug,
                'participantId' => $participant->id,
            ]
        ));

        $listener->shouldHaveReceived('handle', function ($event) use ($conversation, $participant) {
            return $event->conversation->id == $conversation->id
            && $event->participantId == $participant->id;
        });
    }
}
