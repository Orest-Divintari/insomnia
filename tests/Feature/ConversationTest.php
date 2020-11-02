<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    protected $title;
    protected $messageBody;
    protected $participantA;
    protected $participantB;

    public function setUp(): void
    {
        parent::setUp();
        $this->title = 'some title';
        $this->messageBody = 'some message';
        $this->participantA = create(User::class);
        $this->participantB = create(User::class);
    }

    /**
     * Send a post request to start a conversation
     *
     * @param string $title
     * @param string $message
     * @param string $participants
     * @return \Illuminate\Http\Response
     */
    public function postConversation($title = "", $message = "", $participants = "")
    {
        return $this->post(
            route('conversations.store'),
            [
                'title' => $title,
                'message' => $message,
                'participants' => $participants,
            ]
        );
    }

    /** @test */
    public function guests_cannot_see_the_conversation_form()
    {
        $this->get(route('conversations.create'))
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_that_hasnt_verified_the_email_cannot_see_the_conversation_form()
    {
        $user = create(User::class, ['email_verified_at' => null]);
        $this->signIn($user);

        $this->postConversation($title = "", $message = "", $participant = "")
            ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_authenticated_and_verified_user_can_see_the_covnersation_form()
    {
        $this->signIn();

        $this->get(route('conversations.create'))
            ->assertOk();
    }

    /** @test */
    public function guests_cannot_start_a_new_conversation()
    {
        $this->postConversation(
            $this->title,
            $this->messageBody,
            $this->participantA->name
        )->assertRedirect('login');
    }

    /** @test */
    public function a_new_user_that_hasnt_verified_the_email_cannot_start_a_covnersation()
    {
        $user = create(User::class, ['email_verified_at' => null]);
        $this->signIn($user);

        $this->postConversation()
            ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_authenticated_and_verified_user_can_start_a_new_conversation()
    {
        $sender = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
            $this->participantA->name
        );

        $conversation = $sender->conversations()->first();
        $this->assertTrue(
            $conversation->participants->contains($this->participantA->id)
        );
        $this->assertEquals(
            $conversation->title,
            $this->title
        );
        $message = $conversation->messages()->first();
        $this->assertEquals($message->body, $this->messageBody);
    }

    /** @test */
    public function an_authenticated_and_verified_user_can_start_a_new_conversation_with_multiple_participants()
    {
        $sender = $this->signIn();

        $participants = "{$this->participantA->name}, {$this->participantB->name}";

        $this->postConversation(
            $this->title,
            $this->messageBody,
            $participants
        );

        $conversation = $sender->conversations()->first();
        $participantNames = collect([
            $this->participantA->name,
            $this->participantB->name,
            $sender->name,
        ]);

        $this->assertTrue(
            $conversation
                ->participants
                ->pluck('name')
                ->every(function ($value, $key) use ($participantNames) {
                    return $participantNames->contains($value);
                })
        );

        $message = $conversation->messages()->first();
        $this->assertEquals($message->body, $this->messageBody);
    }

    /** @test */
    public function to_start_a_new_conversation_a_title_is_required()
    {
        $sender = $this->signIn();

        $this->postConversation(
            $title = "",
            $this->messageBody,
            $this->participantA->name
        )->assertSessionHasErrors('title');
    }

    /** @test */
    public function to_start_a_new_conversation_a_title_must_be_of_type_string()
    {
        $sender = $this->signIn();
        $title = [1, 2, 3, 4];

        $this->postConversation(
            $title,
            $this->messageBody,
            $this->participantA->name
        )->assertSessionHasErrors('title');
    }

    /** @test */
    public function to_start_a_new_conversation_a_message_is_required()
    {
        $sender = $this->signIn();

        $this->postConversation(
            $this->title,
            $message = "",
            $this->participantA->name
        )->assertSessionHasErrors('message');
    }

    /** @test */
    public function to_start_a_new_conversation_a_message_must_be_of_type_string()
    {
        $sender = $this->signIn();
        $messageBody = ['one message', 'another message'];

        $this->postConversation(
            $this->title,
            $messageBody,
            $this->participantA->name
        )->assertSessionHasErrors('message');
    }

    /** @test */
    public function to_start_a_conversation_a_participant_name_is_required()
    {
        $sender = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
        )->assertSessionHasErrors('participants');
    }

    /** @test */
    public function to_start_new_conversation_the_participant_should_already_exist()
    {
        $sender = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
            'randomName'
        )->assertSessionHasErrors('participants.*');

    }

    /** @test */
    public function to_start_a_new_conversation_all_participant_names_should_exist()
    {
        $sender = $this->signIn();
        $participants = "{$this->participantA->name}, randomName";
        $this->postConversation(
            $this->title,
            $this->messageBody,
            $participants
        )->assertSessionHasErrors('participants.*');
    }

    /** @test */
    public function to_start_a_conversation_a_string_must_be_passed_which_subsequently_is_converted_to_array_for_validation()
    {

    }

}