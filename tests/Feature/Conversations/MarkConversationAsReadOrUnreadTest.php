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
        $this->patch(route('read-conversations.update', $conversation))
            ->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $nonParticipant = $this->signIn();

        $this->patch(route('read-conversations.update', $conversation))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function guests_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->patch(route('unread-conversations.update', $conversation))
            ->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);

        $nonParticipant = $this->signIn();

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->patch(route('unread-conversations.update', $conversation))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authorized_users_can_mark_a_conversation_as_unread()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->patch(route('read-conversations.update', $conversation));

        $this->assertFalse($conversation->hasBeenUpdated);

        $this->patch(route('unread-conversations.update', $conversation))
            ->assertOk();

        $this->assertTrue($conversation->hasBeenUpdated);
    }

    /** @test */
    public function an_authorized_user_can_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->patch(route('read-conversations.update', $conversation))
            ->assertOk();

        $this->assertDatabaseHas('reads', [
            'readable_id' => $conversation->id,
            'readable_type' => 'App\Conversation',
            'user_id' => $conversationStarter->id,
            'read_at' => $conversation->reads()->latest()->first()->read_at,
        ]);

        $this->assertFalse($conversation->fresh()->hasBeenUpdated);
    }
}