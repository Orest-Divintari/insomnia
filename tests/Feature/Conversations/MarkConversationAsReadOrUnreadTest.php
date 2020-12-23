<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class MarkConversationAsReadOrUnreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_mark_a_conversation_as_read()
    {
        $conversation = create(Conversation::class);

        $response = $this->post(
            route('read-conversations.store', $conversation)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $this->assertTrue($conversation->hasBeenUpdated());
        $nonParticipant = $this->signIn();

        $response = $this->post(
            route('read-conversations.store', $conversation)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function guests_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);

        $response = $this->delete(
            route('read-conversations.destroy', $conversation)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);
        $nonParticipant = $this->signIn();

        $response = $this->delete(
            route('read-conversations.destroy', $conversation)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function converastion_participants_mark_a_conversation_as_unread()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $conversationStarter->read($conversation);
        $this->assertFalse($conversation->hasBeenUpdated());

        $this->delete(
            route('read-conversations.destroy', $conversation)
        );

        $this->assertTrue($conversation->hasBeenUpdated());
    }

    /** @test */
    public function a_conversation_participant_can_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $this->assertTrue($conversation->hasBeenUpdated());

        $this->post(
            route('read-conversations.store', $conversation)
        );

        $this->assertFalse($conversation->hasBeenUpdated());
    }
}