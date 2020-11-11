<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
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

        $this->patch(route('hide-conversations.update', $conversation))
            ->assertRedirect('login');
    }

    /** @test */
    public function a_user_cannot_hide_a_conversation_that_has_not_participated_in()
    {
        $conversation = create(Conversation::class);

        $user = $this->signIn();

        $this->patch(route('hide-conversations.update', $conversation))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_participant_of_a_conversation_can_hide_a_specific_conversation()
    {
        $user = $this->signIn();

        $conversation = create(Conversation::class);

        $this->assertCount(1, $user->conversations);

        $this->patch(route('hide-conversations.update', $conversation))
            ->assertOk();

        $this->assertCount(0, $user->fresh()->conversations);
    }

}