<?php

namespace Tests\Feature;

use App\Reply;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageConversationMessagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_update_conversation_messages()
    {
        $randomMessageId = 1;

        $this->patch(
            route('api.messages.update', $randomMessageId),
            ['body' => 'new body']
        )->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_update_a_message()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $message = $conversation->messages()->first();

        $unathorizedUser = $this->signIn();

        $this->patch(
            route('api.messages.update', $message),
            ['body' => 'new body']
        )->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_message_can_be_updated_only_by_the_owner()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $message = $conversation->messages()->first();

        $this->patch(
            route('api.messages.update', $message),
            $updatedMessage = ['body' => 'new body']
        )->assertOk();

        $this->assertEquals(
            Reply::whereId($message->id)->first()->body,
            $updatedMessage['body']
        );
    }

    /** @test */
    public function a_conversation_message_requires_a_body_when_updated()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $message = $conversation->messages()->first();

        $this->patch(
            route('api.messages.update', $message),
            $updatedMessage = ['body' => '']
        )->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_conversation_message_must_be_of_type_string()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $message = $conversation->messages()->first();

        $this->patch(
            route('api.messages.update', $message),
            $updatedMessage = ['body' => []]
        )->assertSessionHasErrors('body');
    }
}