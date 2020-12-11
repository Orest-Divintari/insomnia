<?php

namespace Tests\Feature;

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
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantB->id,
            'admin' => false,
        ]);

        $this->post(
            route(
                'api.conversation-admins.store',
                [$conversation, $participantB->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantB->id,
            'admin' => true,
        ]);
    }

    /** @test */
    public function the_conversation_starter_can_always_set_another_conversation_participant_as_admin()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);

        $conversation = ConversationFactory::withParticipants(
            [$participant->name]
        )->create();

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participant->id,
            'admin' => false,
        ]);

        $this->post(
            route(
                'api.conversation-admins.store',
                [$conversation, $participant->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participant->id,
            'admin' => true,
        ]);
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

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantB->id,
            'admin' => false,
        ]);

        $this->signIn($participantB);
        $this->post(
            route(
                'api.conversation-admins.store',
                [$conversation, $participantA->id]
            )
        )->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantA->id,
            'admin' => false,
        ]);
    }

    /** @test */
    public function the_conversation_starater_caan_always_remove_as_admin_another_conversation_member()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);

        $conversation = ConversationFactory::withParticipants(
            [$participant->name]
        )->create();

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participant->id,
            'admin' => false,
        ]);

        $this->post(
            route(
                'api.conversation-admins.store',
                [$conversation, $participant->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participant->id,
            'admin' => true,
        ]);

        $this->delete(
            route(
                'api.conversation-admins.destroy',
                [$conversation, $participant->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participant->id,
            'admin' => false,
        ]);
    }

    /** @test */
    public function an_conversation_admin_can_remove_as_admin_another_conversation_member()
    {
        $conversationStarter = $this->signIn();
        $participantA = create(User::class);
        $participantB = create(User::class);

        $conversation = ConversationFactory::withParticipants(
            [$participantA->name, $participantB->name]
        )->create();

        $conversation->setAdmin($participantA->id);
        $this->signIn($participantA);
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantB->id,
            'admin' => false,
        ]);

        $this->post(
            route(
                'api.conversation-admins.store',
                [$conversation, $participantB->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantB->id,
            'admin' => true,
        ]);

        $this->delete(
            route(
                'api.conversation-admins.destroy',
                [$conversation, $participantB->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantB->id,
            'admin' => false,
        ]);
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

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantA->id,
            'admin' => false,
        ]);

        $this->post(
            route(
                'api.conversation-admins.store',
                [$conversation, $participantA->id]
            )
        );

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantA->id,
            'admin' => true,
        ]);

        $this->signIn($participantB);
        $this->delete(
            route('api.conversation-admins.destroy',
                [$conversation, $participantA->id]
            ))->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participantA->id,
            'admin' => true,
        ]);
    }
}