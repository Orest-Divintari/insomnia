<?php

namespace Tests\Feature\Conversations;

use App\Models\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteConversationParticipantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_conversation_starter_may_invite_another_participant_in_a_conversation()
    {
        $john = $this->signIn();
        $george = create(User::class);
        $orestis = create(User::class);
        $conversation = ConversationFactory::by($john)->withParticipants([$george->name])->create();

        $this->post(route('ajax.conversation-participants.store', $conversation), ['participants' => $orestis->name]);

        $this->assertTrue($conversation->hasParticipant($orestis));
    }

    /** @test */
    public function a_conversation_admin_may_invite_another_participant_in_the_conversation()
    {
        $john = $this->signIn();
        $george = create(User::class);
        $orestis = create(User::class);
        $conversation = ConversationFactory::by($john)->withParticipants([$george->name])->create();
        $conversation->setAdmin($george->id);
        $this->signIn($george);

        $this->post(route('ajax.conversation-participants.store', $conversation), ['participants' => $orestis->name]);

        $this->assertTrue($conversation->hasParticipant($orestis));
    }

    /** @test */
    public function authorized_users_may_not_invite_another_user_that_already_participants_in_the_conversation()
    {
        $john = $this->signIn();
        $george = create(User::class);
        $conversation = ConversationFactory::by($john)->withParticipants([$george->name])->create();

        $response = $this->postJson(
            route('ajax.conversation-participants.store', $conversation),
            ['participants' => $george->name]
        );

        $response->assertJsonMissingValidationErrors(["You may not start a conversation with the following recipients: {$george->name}."]);
    }
}
