<?php

namespace Tests\Feature\Messages;

use App\Conversation;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateConversationMessageTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid message.';

    /** @test */
    public function guests_cannot_add_a_message_to_a_conversation()
    {
        $conversationSlug = 'randomSlug';

        $response = $this->post(
            route('ajax.messages.store', $conversationSlug),
            ['body' => 'some message']
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_add_messages_to_conversation()
    {
        $conversation = create(Conversation::class);
        $message = ['body' => 'some message'];
        $unathorizedUser = $this->signIn();

        $response = $this->post(
            route('ajax.messages.store', $conversation),
            $message
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_message_requires_a_body()
    {
        $this->signIn();
        $conversation = ConversationFactory::create();
        $message = ['body' => ''];

        $response = $this->postJson(
            route('ajax.messages.store', $conversation),
            $message
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_message_must_be_string()
    {
        $this->signIn();
        $conversation = ConversationFactory::create();
        $nonStringMessage = array(15);
        $message = ['body' => $nonStringMessage];

        $response = $this->postJson(
            route('ajax.messages.store', $conversation),
            $message
        );

        $response->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function the_conversation_starter_can_add_a_new_message_to_the_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $message = ['body' => 'some message'];

        $this->post(
            route('ajax.messages.store', $conversation),
            $message
        );

        $this->assertDatabaseHas('replies', [
            'repliable_type' => 'App\Conversation',
            'repliable_id' => $conversation->id,
            'user_id' => $conversationStarter->id,
            'body' => $message['body'],
        ]);
    }

    /** @test */
    public function a_conversation_participant_can_add_a_new_message_to_the_conversation()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->by($conversationStarter)->create();
        $message = ['body' => 'some message'];
        $this->signIn($participant);

        $this->post(
            route('ajax.messages.store', $conversation),
            $message
        );

        $this->assertDatabaseHas('replies', [
            'repliable_type' => 'App\Conversation',
            'repliable_id' => $conversation->id,
            'user_id' => $participant->id,
            'body' => $message['body'],
        ]);
    }
}
