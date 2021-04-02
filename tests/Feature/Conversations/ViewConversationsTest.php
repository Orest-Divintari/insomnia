<?php

namespace Tests\Feature\Conversations;

use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ViewConversationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_view_a_conversation()
    {
        $someRandomConversationSlug = 'asdf';

        $response = $this->get(
            route('conversations.show', $someRandomConversationSlug)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_view_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $notParticipant = $this->signIn();

        $response = $this->get(route('conversations.show', $conversation));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}