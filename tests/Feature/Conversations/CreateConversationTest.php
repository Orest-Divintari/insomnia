<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\ConversationParticipant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateConversationTest extends TestCase
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
    public function postConversation($title = "", $message = "", $participants = "", $admin = false)
    {
        return $this->post(
            route('conversations.store'),
            [
                'title' => $title,
                'message' => $message,
                'participants' => $participants,
                'admin' => $admin,
            ]
        );
    }

    /** @test */
    public function guests_cannot_see_the_conversation_form()
    {
        $response = $this->get(route('conversations.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_that_hasnt_verified_the_email_cannot_see_the_conversation_form()
    {
        $user = create(User::class, ['email_verified_at' => null]);
        $this->signIn($user);

        $response = $this->postConversation($title = "", $message = "", $participant = "");

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_authenticated_and_verified_user_can_see_the_covnersation_form()
    {
        $this->signIn();

        $response = $this->get(route('conversations.create'));

        $response->assertOk();
    }

    /** @test */
    public function an_authenticated_and_verified_user_can_pass_a_participant_name_to_convrersation_form()
    {
        $this->signIn();
        $participant = create(User::class);

        $response = $this->get(
            route('conversations.create')
            . '?add_participant='
            . $participant->name
        );

        $response->assertSee($participant->name);
    }

    /** @test */
    public function guests_cannot_start_a_new_conversation()
    {
        $response = $this->postConversation(
            $this->title,
            $this->messageBody,
            $this->participantA->name
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function a_new_user_that_hasnt_verified_the_email_cannot_start_a_covnersation()
    {
        $user = create(User::class, ['email_verified_at' => null]);
        $this->signIn($user);

        $response = $this->postConversation();

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_authenticated_and_verified_user_can_start_a_new_conversation()
    {
        $conversationStarter = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
            $this->participantA->name
        );

        $conversation = $conversationStarter->conversations()->first();
        $this->assertTrue(
            $conversation->participants
                ->contains($this->participantA->id)
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
        $conversationStarter = $this->signIn();
        $participants = "{$this->participantA->name}, {$this->participantB->name}";

        $this->postConversation(
            $this->title,
            $this->messageBody,
            $participants
        );

        $conversation = $conversationStarter->conversations()->first();
        $participantNames = collect([
            $this->participantA->name,
            $this->participantB->name,
            $conversationStarter->name,
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
    public function an_authenticated_user_can_start_a_conversation_and_set_all_participants_as_admin()
    {
        $conversationStarter = $this->signIn();
        $participants = "{$this->participantA->name}, {$this->participantB->name}";

        $this->postConversation(
            $this->title,
            $this->messageBody,
            $participants,
            $admin = true
        );

        $conversation = $conversationStarter->conversations()->first();
        $participantNames = collect([
            $this->participantA->name,
            $this->participantB->name,
            $conversationStarter->name,
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
        $this->assertTrue(
            ConversationParticipant::where('user_id', $this->participantA->id)
                ->where('conversation_id', $conversation->id)
                ->first()
                ->admin
        );
        $this->assertTrue(
            ConversationParticipant::where('user_id', $this->participantB->id)
                ->where('conversation_id', $conversation->id)
                ->first()
                ->admin
        );
    }

    /** @test  */
    public function the_converstation_starter_is_added_as_a_participant_to_the_newly_created_conversation()
    {
        $conversationStarter = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
            $this->participantA->name
        );

        $conversation = $conversationStarter->conversations->first();
        $this->assertTrue(
            $conversation->participants->contains($conversationStarter->id)
        );
    }

    /** @test */
    public function to_start_a_new_conversation_a_title_is_required()
    {
        $conversationStarter = $this->signIn();

        $response = $this->postConversation(
            $title = "",
            $this->messageBody,
            $this->participantA->name
        );

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function to_start_a_new_conversation_a_title_must_be_of_type_string()
    {
        $conversationStarter = $this->signIn();
        $title = [1, 2, 3, 4];

        $response = $this->postConversation(
            $title,
            $this->messageBody,
            $this->participantA->name
        );

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function to_start_a_new_conversation_a_message_is_required()
    {
        $conversationStarter = $this->signIn();

        $response = $this->postConversation(
            $this->title,
            $message = "",
            $this->participantA->name
        );

        $response->assertSessionHasErrors('message');
    }

    /** @test */
    public function to_start_a_new_conversation_a_message_must_be_of_type_string()
    {
        $conversationStarter = $this->signIn();
        $messageBody = ['one message', 'another message'];

        $response = $this->postConversation(
            $this->title,
            $messageBody,
            $this->participantA->name
        );

        $response->assertSessionHasErrors('message');
    }

    /** @test */
    public function to_start_a_conversation_a_participant_name_is_required()
    {
        $conversationStarter = $this->signIn();

        $response = $this->postConversation(
            $this->title,
            $this->messageBody,
            $participant = ''
        );

        $response->assertSessionHasErrors('participants');
    }

    /** @test */
    public function to_start_new_conversation_the_participant_should_already_exist()
    {
        $conversationStarter = $this->signIn();
        $nonExistingUser = 'john';

        $response = $this->postConversation(
            $this->title,
            $this->messageBody,
            $nonExistingUser
        );

        $response->assertSessionHasErrors(
            ['participants.*' => ['The following participant could not be found: ' . $nonExistingUser]]
        );
    }

    /** @test */
    public function to_start_a_new_conversation_all_participant_names_should_exist()
    {
        $conversationStarter = $this->signIn();
        $nonExistingUser = 'randomName';
        $anotherNonExistingUser = 'nonExisting';
        $participants = "{$anotherNonExistingUser}, {$nonExistingUser}";

        $response = $this->postConversation(
            $this->title,
            $this->messageBody,
            $participants
        );

        $response->assertSessionHasErrors(
            ['participants.*' =>
                ['The following participant could not be found: ' . $anotherNonExistingUser],
            ],
        );
        $response->assertSessionHasErrors(
            ['participants.*' =>
                ['The following participant could not be found: ' . $anotherNonExistingUser],
            ]
        );
    }

    /** @test */
    public function a_conversation_requires_a_unique_slug()
    {
        $conversationStarter = $this->signIn();
        $this->assertUniqueSlug('some title', 'some-title');
        $this->assertUniqueSlug('some title', 'some-title.2');
        $this->assertUniqueSlug('some title', 'some-title.3');
        $this->assertUniqueSlug('some title 55', 'some-title-55');
        $this->assertUniqueSlug('some title 55', 'some-title-55.2');
    }

    public function assertUniqueSlug($title, $slug)
    {
        $this->postConversation(
            $title,
            $this->messageBody,
            $this->participantA->name
        );

        $this->assertEquals(
            Conversation::latest('id')->first()->slug,
            $slug
        );
    }

}