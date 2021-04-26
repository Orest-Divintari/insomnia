<?php

namespace Tests\Feature\Messages;

use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ReadConversationMessagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthorised_users_cannot_read_messages_of_conversations_that_they_are_not_participants()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $message = $conversation->messages()->first();
        $nonParticipant = $this->signIn();

        $response = $this->get(route('messages.show', $message));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function conversation_participants_can_read_their_messages()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $message = $conversation->messages()->first();

        $response = $this->get(
            route('messages.show', $message)
        );

        $response->assertRedirect($message->path);
    }
}