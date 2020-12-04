<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class InviteConversationParticipantsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_conversation_admin_may_invite_a_single_member_to_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $newMember = create(User::class);

        $this->postJson(
            route('api.invite-conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        );

        $this->assertEquals(
            $conversation->id,
            $newMember->conversations->first()->id
        );
    }

    /** @test */
    public function a_conversation_admin_may_invite_multiple_members_to_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $newMembersNames = "{$john->name}, {$doe->name}";

        $this->postJson(
            route('api.invite-conversation-participants.store', $conversation),
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
            route('api.invite-conversation-participants.store', $conversation),
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
            route('api.invite-conversation-participants.store', $conversation),
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
            route('api.invite-conversation-participants.store', $conversation),
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
            route('api.invite-conversation-participants.store', $conversation),
            ['participants' => $newMember->name]
        )->assertStatus(Response::HTTP_FORBIDDEN);
    }
}