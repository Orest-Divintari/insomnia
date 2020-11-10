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
    public function guests_cannot_read_conversation_messages()
    {
        $this->get(route('api.messages.index', ['conversation' => 1]))
            ->assertRedirect('login');
    }

    /** @test */
    public function authenticated_users_cannot_read_messages_of_conversations_that_they_are_not_participants()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $message = $conversation->messages()->first();

        $nonParticipant = $this->signIn();

        $this->get(route('api.messages.show', $message))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authenticated_participants_of_a_conversation_can_view_the_messages()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $message = $conversation->messages()->first();

        $this->get(
            route('api.messages.show', $message)
        )->assertRedirect(
            route('conversations.show', $message->repliable) .
            '?page=' . $message->pageNumber .
            '#convMessage-' . $message->id
        );
    }
}