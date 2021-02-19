<?php

namespace Tests\Feature\Conversations;

use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ConversationAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_conversation_admin_can_set_another_conversation_participant_as_admin()
    {
        $conversationStarter = $this->signIn();
        $participantA = create(User::class);
        $participantB = create(User::class);
        $conversation = ConversationFactory::withParticipants(
            [$participantA->name, $participantB->name]
        )->create();
        $conversation->setAdmin($participantA->id);
        $this->signIn($participantA);
        $this->assertFalse($conversation->isAdmin($participantB));

        $this->patch(route(
            'ajax.conversation-admins.update',
            [$conversation, $participantB->id]
        ));

        $this->assertTrue($conversation->isAdmin($participantB));
    }

    /** @test */
    public function the_conversation_starter_can_always_set_another_conversation_participant_as_admin()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants(
            [$participant->name]
        )->create();
        $this->assertFalse($conversation->isAdmin($participant));

        $this->patch(route(
            'ajax.conversation-admins.update',
            [$conversation, $participant->id]
        ));

        $this->assertTrue($conversation->isAdmin($participant));
    }

    /** @test */
    public function unathorized_users_cannot_set_as_admin_another_conversation_member()
    {
        $conversationStarter = $this->signIn();
        $participantA = create(User::class);
        $participantB = create(User::class);
        $conversation = ConversationFactory::withParticipants(
            [$participantA->name, $participantB->name]
        )->create();
        $this->assertFalse($conversation->isAdmin($participantB));
        $this->signIn($participantB);

        $response = $this->patch(route(
            'ajax.conversation-admins.update',
            [$conversation, $participantA->id]
        ));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertFalse($conversation->isAdmin($participantB));
    }

    /** @test */
    public function the_conversation_starter_can_always_remove_as_admin_another_conversation_member()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])->create();
        $conversation->setAdmin($participant->id);

        $this->delete(route(
            'ajax.conversation-admins.destroy',
            [$conversation, $participant->id]
        ));

        $this->assertFalse($conversation->isAdmin($participant));
    }

    /** @test */
    public function a_conversation_admin_can_remove_as_admin_another_conversation_member()
    {
        $conversationStarter = $this->signIn();
        $participantA = create(User::class);
        $participantB = create(User::class);
        $conversation = ConversationFactory::withParticipants(
            [$participantA->name, $participantB->name]
        )->create();
        $conversation->setAdmin($participantA->id);
        $conversation->setAdmin($participantB->id);
        $this->assertTrue($conversation->isAdmin($participantB));

        $this->delete(route(
            'ajax.conversation-admins.destroy',
            [$conversation, $participantB->id]
        ));

        $this->assertFalse($conversation->isAdmin($participantB));
    }

    /** @test */
    public function unathorized_users_cannot_remove_as_admin_another_conversation_member()
    {
        $conversationStarter = $this->signIn();
        $participantA = create(User::class);
        $participantB = create(User::class);
        $conversation = ConversationFactory::withParticipants(
            [$participantA->name, $participantB->name]
        )->create();
        $conversation->setAdmin($participantA->id);
        $this->assertTrue($conversation->isAdmin($participantA));
        $this->signIn($participantB);

        $response = $this->delete(
            route('ajax.conversation-admins.destroy',
                [$conversation, $participantA->id]
            )
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertTrue($conversation->isAdmin($participantA));
    }
}
