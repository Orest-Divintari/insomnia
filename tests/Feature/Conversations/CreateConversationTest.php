<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
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
        $this->withExceptionHandling();
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
    public function to_start_a_new_conversation_a_title_is_required()
    {
        $conversationStarter = $this->signIn();

        $this->postConversation(
            $title = "",
            $this->messageBody,
            $this->participantA->name
        )->assertSessionHasErrors('title');
    }

    /** @test */
    public function to_start_a_new_conversation_a_title_must_be_of_type_string()
    {
        $conversationStarter = $this->signIn();
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
        $conversationStarter = $this->signIn();

        $this->postConversation(
            $this->title,
            $message = "",
            $this->participantA->name
        )->assertSessionHasErrors('message');
    }

    /** @test */
    public function to_start_a_new_conversation_a_message_must_be_of_type_string()
    {
        $conversationStarter = $this->signIn();
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
        $conversationStarter = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
        )->assertSessionHasErrors('participants');
    }

    /** @test */
    public function to_start_new_conversation_the_participant_should_already_exist()
    {
        $conversationStarter = $this->signIn();

        $this->postConversation(
            $this->title,
            $this->messageBody,
            'randomName'
        )->assertSessionHasErrors('participants.*');

    }

    /** @test */
    public function to_start_a_new_conversation_all_participant_names_should_exist()
    {
        $conversationStarter = $this->signIn();
        $participants = "{$this->participantA->name}, randomName";
        $this->postConversation(
            $this->title,
            $this->messageBody,
            $participants
        )->assertSessionHasErrors('participants.*');
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