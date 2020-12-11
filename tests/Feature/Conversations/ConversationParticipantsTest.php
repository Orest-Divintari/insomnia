<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ConversationParticipantsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_conversation_admin_may_invite_a_single_participant_to_conversation()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->by($conversationStarter)
            ->create();
        $conversation->setAdmin($participant->id);
        $this->signIn($participant);
        $newMember = create(User::class);

        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        );

        $this->assertEquals(
            $conversation->id,
            $newMember->conversations->first()->id
        );
    }

    /** @test */
    public function the_conversation_starter_can_always_invite_a_new_participant_to_the_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $newMember = create(User::class);

        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        );

        $this->assertEquals(
            $conversation->id,
            $newMember->conversations->first()->id
        );
    }

    /** @test */
    public function the_conversation_starter_can_always_invite_multiple_participants_to_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $newMembersNames = "{$john->name}, {$doe->name}";

        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMembersNames]
        );

        $this->assertEquals(
            $conversation->id,
            $john->conversations->first()->id
        );

        $this->assertEquals(
            $conversation->id,
            $doe->conversations->first()->id
        );
    }

    /** @test */
    public function a_conversation_admin_can_invite_multiple_participants_to_the_conversation()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->by($conversationStarter)
            ->create();
        $conversation->setAdmin($participant->id);
        $john = create(User::class);
        $doe = create(User::class);
        $newMembersNames = "{$john->name}, {$doe->name}";

        $this->signIn($participant);

        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMembersNames]
        );

        $this->assertEquals(
            $conversation->id,
            $john->conversations->first()->id
        );

        $this->assertEquals(
            $conversation->id,
            $doe->conversations->first()->id
        );
    }

    /** @test */
    public function a_conversation_participant_invitation_requires_a_username()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();

        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => []]
        )->assertJson(
            ['participants' => ['Please enter at least one username.']]
        );

    }

    /** @test */
    public function guests_cannot_add_participants_to_a_conversation()
    {
        $guestUser = create(User::class);
        $conversation = create(Conversation::class);
        $newMember = create(User::class);

        $this->post(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        )->assertRedirect('login');
    }

    /** @test */
    public function only_members_that_already_exist_in_the_database_can_be_invited()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $nonExistingMember = 'john';

        $response = $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $nonExistingMember]
        )->assertJson(
            ['participants.0' => ["You may not start a conversation with the following participant: " . $nonExistingMember]]
        );

    }

    /** @test */
    public function unauthorized_users_cannot_add_new_participant_to_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $unathorizedUser = create(User::class);
        $newMember = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$unathorizedUser->name])
            ->create();

        $this->signIn($unathorizedUser);

        $this->post(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        )->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function the_conversatio_starter_can_always_remove_a_member_from_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $newMember = create(User::class);

        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        );

        $this->assertEquals(
            $conversation->id,
            $newMember->conversations->first()->id
        );

        $this->deleteJson(
            route('api.conversation-participants.destroy', [$conversation, $newMember->id]),
        );

        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $newMember->id,
        ]);
    }
    /** @test */
    public function a_conversation_admin_may_remove_a_member_from_the_conversation_conversation()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->by($conversationStarter)
            ->create();
        $conversation->setAdmin($participant->id);
        $newMember = create(User::class);

        $this->signIn($participant);
        $this->postJson(
            route('api.conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        );

        $this->assertEquals(
            $conversation->id,
            $newMember->conversations->first()->id
        );

        $this->deleteJson(
            route('api.conversation-participants.destroy', [$conversation, $newMember->id]),
        );

        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $newMember->id,
        ]);
    }

    /** @test */
    public function unauthorized_users_cannot_remove_a_participant_from_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $unathorizedUser = create(User::class);

        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->signIn($unathorizedUser);

        $this->delete(
            route('api.conversation-participants.destroy', [$conversation, $participant->id]),
        )->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $participant->id,
        ]);
    }

}