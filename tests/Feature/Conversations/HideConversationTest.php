<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class HideConversationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_hide_a_conversation()
    {
        $conversation = create(Conversation::class);

        $response = $this->patch(route('hide-conversations.update', $conversation));

        $response->assertRedirect('login');
    }

    /** @test */
    public function a_user_cannot_hide_a_conversation_that_has_not_participated_in()
    {
        $conversation = create(Conversation::class);
        $this->signIn();

        $response = $this->patch(route('hide-conversations.update', $conversation));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_participant_of_a_conversation_can_hide_a_conversation()
    {
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $this->assertCount(1, $user->conversations);

        $response = $this->patch(
            route('hide-conversations.update', $conversation)
        );

        $response->assertOk();
        $this->assertCount(0, $user->fresh()->conversations);
    }

    /** @test */
    public function when_a_new_message_is_addded_to_a_hidden_conversation_then_the_conversation_is_unhidden()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $conversation->hideFrom($participant);
        $this->assertCount(0, $participant->conversations);

        $this->post(
            route('api.messages.store', $conversation),
            ['body' => 'new message']
        );

        $this->assertCount(1, $participant->fresh()->conversations);
    }

}