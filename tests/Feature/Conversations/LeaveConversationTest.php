<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class LeaveConversationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_leave_a_conversation()
    {
        $conversation = create(Conversation::class);

        $response = $this->patch(
            route('ajax.leave-conversations.update', $conversation)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function a_user_cannot_leave_a_conversation_that_has_not_participated_in()
    {
        $conversation = create(Conversation::class);
        $user = $this->signIn();

        $response = $this->patch(
            route('ajax.leave-conversations.update', $conversation)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_participant_of_a_conversation_can_leave_a_conversation()
    {
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $this->assertCount(1, $user->conversations);

        $response = $this->patch(
            route('ajax.leave-conversations.update', $conversation)
        );

        $response->assertOk();
        $this->assertCount(0, $user->fresh()->conversations);
    }
}