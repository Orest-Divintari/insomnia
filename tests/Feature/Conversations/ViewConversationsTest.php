<?php

namespace Tests\Feature\Conversations;

use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ViewConversationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $conversation;
    protected $conversationStarter;
    protected $participant;

    public function setUp(): void
    {
        parent::setUp();

        $this->conversationStarter = $this->signIn();
        $this->participant = create(User::class);
        $this->conversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();
    }

    /** @test */
    public function guests_cannot_view_a_conversation()
    {
        $someRandomConversationSlug = 'asdf';
        Auth::logout();

        $response = $this->get(
            route('conversations.show', $someRandomConversationSlug)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_view_a_conversation()
    {
        $unathorizedUser = $this->signIn();

        $response = $this->get(route('conversations.show', $this->conversation));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_returns_the_conversation()
    {
        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertEquals($this->conversation->id, $response['conversation']['id']);
    }

    /** @test */
    public function it_shows_whether_the_conversation_is_starred()
    {
        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertFalse($response['conversation']['starred']);
    }

    /** @test */
    public function it_marks_the_conversation_as_read_when_is_visited()
    {
        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertFalse($response['conversation']['has_been_updated']);
    }

    /** @test */
    public function it_returns_the_participants()
    {
        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertEquals(
            $this->conversationStarter->id,
            $response['participants'][0]['id']
        );
        $this->assertEquals(
            $this->participant->id,
            $response['participants'][1]['id']
        );
    }

    /** @test */
    public function it_shows_whether_a_participant_is_admin_of_the_conversation()
    {
        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertTrue($response['participants'][0]['conversation_admin']);
        $this->assertFalse($response['participants'][1]['conversation_admin']);
    }

    /** @test */
    public function it_returns_the_associated_messages()
    {
        $message = $this->conversation->messages()->first();

        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertEquals($message->id, $response['messages']['data']['0']['id']);
    }

    /** @test */
    public function it_returns_the_message_likes_count()
    {
        $message = $this->conversation->messages()->first();
        $message->like($this->conversationStarter);

        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertEquals(1, $response['messages']['data'][0]['likes_count']);
    }

    /** @test */
    public function it_shows_whether_a_message_has_been_liked_by_the_visitor()
    {
        $message = $this->conversation->messages()->first();
        $message->like($this->conversationStarter);

        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertTrue($response['messages']['data'][0]['is_liked']);
    }

    /** @test */
    public function it_shows_whether_a_message_has_not_been_liked_by_the_visitor()
    {
        $message = $this->conversation->messages()->first();

        $response = $this->get(route('conversations.show', $this->conversation));

        $this->assertFalse($response['messages']['data'][0]['is_liked']);
    }

    /** @test */
    public function it_returns_the_list_of_conversations()
    {
        $response = $this->get(route('conversations.index'));

        $this->assertEquals($this->conversation->id, $response['conversations'][0]['id']);
    }

    /** @test */
    public function unverified_users_should_not_see_the_start_conversation_button()
    {
        $user = $this->signInUnverified();
        $response = $this->get(route('conversations.index'));

        $response->assertDontSee('Start Conversation');
    }

    /** @test */
    public function verified_users_should_not_see_the_start_conversation_button()
    {
        $this->signIn();

        $response = $this->get(route('conversations.index'));

        $response->assertSee('Start Conversation');
    }

    /** @test */
    public function when_a_conversation_is_visited_is_marked_as_read()
    {
        $response = $this->get(route('conversations.index'));

        $this->assertFalse($response['conversations'][0]['has_been_updated']);
    }

    /** @test */
    public function it_shows_whether_a_conversation_from_the_list_has_been_marked_as_unread()
    {
        $this->conversation->unread($this->conversationStarter);

        $response = $this->get(route('conversations.index'));

        $this->assertTrue($response['conversations'][0]['has_been_updated']);
    }

    /** @test */
    public function it_shows_whether_a_conversation_from_the_list_is_starred()
    {
        $this->conversation->starredBy($this->conversationStarter);

        $response = $this->get(route('conversations.index'));

        $this->assertTrue($response['conversations'][0]['starred']);
    }

    /** @test */
    public function it_shows_whether_a_conversation_from_the_list_is_not_starred()
    {
        $response = $this->get(route('conversations.index'));

        $this->assertFalse($response['conversations'][0]['starred']);
    }

    /** @test */
    public function it_returns_the_most_recent_message_of_a_conversation()
    {
        Carbon::setTestNow(Carbon::now()->addDay());
        $recentMessage = $this->conversation->addMessage(['body' => $this->faker()->sentence()]);

        $response = $this->get(route('conversations.index'));

        $this->assertEquals($recentMessage->id, $response['conversations'][0]['recentMessage']['id']);
    }

    /** @test */
    public function it_returns_the_conversation_starter_of_each_conversation()
    {
        $response = $this->get(route('conversations.index'));

        $this->assertEquals($this->conversationStarter->id, $response['conversations'][0]['starter']['id']);
    }

    /** @test */
    public function it_returns_the_number_of_messages_of_each_conversation()
    {
        $response = $this->get(route('conversations.index'));

        $this->assertEquals(1, $response['conversations'][0]['messages_count']);
    }

    /** @test */
    public function it_returns_the_number_of_participants_of_each_conversation()
    {
        $response = $this->get(route('conversations.index'));

        $this->assertEquals(2, $response['conversations'][0]['participants_count']);
    }

    /** @test */
    public function it_shows_first_the_conversation_that_user_has_interacted_recently()
    {
        Carbon::setTestNow(Carbon::now()->addDay());
        $newConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();

        $response = $this->get(route('conversations.index'));

        $this->assertEquals(
            $newConversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(
            $this->conversation->id,
            $response['conversations'][1]['id']
        );
    }

    /** @test */
    public function it_does_not_return_the_conversations_that_a_user_has_left()
    {
        $this->conversation->leftby($this->conversationStarter);
        $visibleConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();

        $response = $this->get(route('conversations.index'));

        $this->assertEquals(
            $visibleConversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(1, count($response['conversations']));
    }

    /** @test */
    public function it_does_not_return_the_conversations_that_have_not_been_updated_and_have_been_hidden()
    {
        $this->conversation->hideFrom($this->conversationStarter);
        $visibleConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();

        $response = $this->get(route('conversations.index'));

        $this->assertEquals(
            $visibleConversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(1, count($response['conversations']));
    }

    /** @test */
    public function it_returns_the_conversations_that_have_been_updated_after_have_been_hidden()
    {
        $this->conversation->hideFrom($this->conversationStarter);
        $this->conversation->addMessage(
            ['body' => $this->faker()->sentence()],
            $this->participant
        );
        $newConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();

        $response = $this->get(route('conversations.index'));

        $this->assertEquals(
            $this->conversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(
            $newConversation->id,
            $response['conversations'][1]['id']
        );
    }

    /** @test */
    public function it_returns_the_unread_conversations()
    {
        $unreadConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();
        $unreadConversation->unread($this->conversationStarter);

        $response = $this->get(route('conversations.index', ['unread' => true]));

        $this->assertEquals(
            $unreadConversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(1, count($response['conversations']));
        $this->assertEquals(1, $response['conversationFilters']['unread']);
    }

    /** @test */
    public function it_returns_the_starred_conversations()
    {
        $this->conversation->starredBy($this->conversationStarter);
        $unstarredConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$this->participant->name])
            ->create();

        $response = $this->get(route('conversations.index', ['starred' => true]));

        $this->assertEquals(
            $this->conversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(1, count($response['conversations']));
        $this->assertEquals(1, $response['conversationFilters']['starred']);
    }

    /** @test */
    public function it_returns_conversations_that_are_created_by_a_given_username()
    {
        $this->signIn($this->participant);
        $undesiredConversation = ConversationFactory::by($this->participant)
            ->withParticipants([$this->conversationStarter->name])
            ->create();
        $this->signIn($this->conversationStarter);

        $response = $this->get(route('conversations.index', ['started_by' => $this->conversationStarter->name]));

        $this->assertEquals(
            $this->conversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(1, count($response['conversations']));
        $this->assertEquals($this->conversationStarter->name, $response['conversationFilters']['startedBy']);
    }

    /** @test */
    public function it_returns_the_conversations_that_a_given_user_participates_in()
    {
        $anotherParticipant = create(User::class);
        $undesiredConversation = ConversationFactory::by($this->conversationStarter)
            ->withParticipants([$anotherParticipant->name])
            ->create();

        $response = $this->get(route('conversations.index', ['received_by' => $this->participant->name]));

        $this->assertEquals(
            $this->conversation->id,
            $response['conversations'][0]['id']
        );
        $this->assertEquals(1, count($response['conversations']));
        $this->assertEquals($this->participant->name, $response['conversationFilters']['receivedBy']);
    }

    /** @test */
    public function it_returns_conversations_created_only_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $this->signIn($doe);
        $conversationByDoe = ConversationFactory::by($doe)->withParticipants([$john->name])->create();
        $this->signIn($bob);
        $conversationByBob = ConversationFactory::by($bob)->withParticipants([$john->name])->create();
        $this->signIn($john);
        $john->ignore($doe);

        $response = $this->get(route('conversations.index'));

        $conversations = collect($response['conversations']->items());
        $this->assertCount(1, $conversations);
        $this->assertFalse($conversations->search(function ($conversation) use ($conversationByDoe) {
            return $conversation->id == $conversationByDoe->id;
        }));
    }

    /** @test */
    public function it_returns_conversations_only_by_users_that_are_not_ignored_with_ajax_request()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $this->signIn($doe);
        $conversationByDoe = ConversationFactory::by($doe)->withParticipants([$john->name])->create();
        $this->signIn($bob);
        $conversationByBob = ConversationFactory::by($bob)->withParticipants([$john->name])->create();
        $this->signIn($john);
        $john->ignore($doe);

        $response = $this->get(route('ajax.conversations.index'));
        $conversations = collect($response->json());
        $this->assertCount(1, $conversations);
        $this->assertFalse($conversations->search(function ($conversation) use ($conversationByDoe) {
            return $conversation['id'] == $conversationByDoe->id;
        }));
    }

    /** @test */
    public function it_returns_all_messages_of_a_conversation_even_by_users_that_are_ignored()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $bob = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$doe->name, $bob->name])
            ->create();

        $messageByDoe = $conversation->addMessage(['body' => $this->faker()->sentence()], $doe);
        $conversation->addMessage(['body' => $this->faker()->sentence()], $bob);
        $john->ignore($doe);

        $response = $this->get(route('conversations.show', $conversation));

        $messages = collect($response['messages']['data']);
        $this->assertCount(3, $messages);
        $ignoredMessage = $messages->firstWhere('id', $messageByDoe->id);

        $this->assertTrue($ignoredMessage['creator_ignored_by_visitor']);

        $unignoredMessages = $messages->filter(function ($message) use ($ignoredMessage) {
            return $message['id'] == $ignoredMessage['id'];
        });
        $unignoredMessages->every(function ($message) {
            return !$message['creator_ignored_by_visitor'];
        });
    }

}