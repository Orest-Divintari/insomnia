<?php

namespace Tests\Feature\Messages;

use App\Models\Reply;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageConversationMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid message.';

    /** @test */
    public function guests_cannot_update_conversation_messages()
    {
        $randomMessageId = 1;

        $response = $this->patch(
            route('ajax.messages.update', $randomMessageId),
            ['body' => 'new body']
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_update_a_message()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();
        $message = $conversation->messages()->first();
        $unathorizedUser = $this->signIn();

        $response = $this->patch(
            route('ajax.messages.update', $message),
            ['body' => 'new body']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_message_can_be_updated_only_by_the_creator()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();
        $message = $conversation->messages()->first();

        $this->patch(
            route('ajax.messages.update', $message),
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

        $response = $this->patchJson(
            route('ajax.messages.update', $message),
            $updatedMessage = ['body' => '']
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_conversation_message_must_be_of_type_string()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();
        $message = $conversation->messages()->first();
        $nonStringMessage = array(15);

        $response = $this->patchJson(
            route('ajax.messages.update', $message),
            $updatedMessage = ['body' => $nonStringMessage]
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

}
