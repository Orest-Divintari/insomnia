<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\Read;
use App\User;
use Carbon\Carbon;
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

    /** @test */
    public function authorised_users_can_view_a_conversation_and_the_associated_messages()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();

        $response = $this->getJson(
            route('conversations.show', $conversation)
        )->json();

        $participants = $response['participants'];
        $messages = $response['messages']['data'][0];
        $returnedConversation = $response['conversation'];
        $this->assertEquals($conversationStarter->id, $participants[0]['id']);
        $this->assertEquals($participant->id, $participants[1]['id']);
        $this->assertEquals($message['body'], $messages['body']);
        $this->assertTrue(array_key_exists('likes_count', $messages));
        $this->assertTrue(array_key_exists('is_liked', $messages));
        $this->assertFalse($returnedConversation['has_been_updated']);
        $this->assertFalse($returnedConversation['starred']);
        $this->assertEquals($conversationStarter->id, $messages['poster']['id']);
        $this->assertEquals($conversation->id, $returnedConversation['id']);
    }

    /** @test */
    public function a_user_can_view_his_own_visible_conversations()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();
        $conversation->
            messages()
            ->first()
            ->update(
                ['created_at' => Carbon::now()->subHour()]
            );
        $newMessage = $conversation->addMessage('new message');

        $conversations = $this->getJson(route('conversations.index'))
            ->json()['data'];

        $this->assertEquals(2, $conversations[0]['messages_count']);
        $this->assertEquals(2, $conversations[0]['participants_count']);
        $this->assertEquals($conversation->type, $conversations[0]['type']);
        $this->assertEquals(
            $newMessage->id,
            $conversations[0]['recent_message']['id']
        );
        $this->assertEquals(
            $conversationStarter->id,
            $conversations[0]['starter']['id']
        );
        $this->assertEquals(
            $newMessage->poster->id,
            $conversations[0]['recent_message']['poster']['id']
        );
        $this->assertFalse($conversations[0]['has_been_updated']);
        $this->assertFalse($conversations[0]['starred']);
        $this->assertTrue(
            collect($conversations[0]['participants'])->pluck('id')
                ->every(function ($value, $key) use ($conversation) {
                    return $conversation
                        ->participants
                        ->pluck('id')
                        ->contains($value);
                })
        );
    }

    /** @test */
    public function a_user_will_not_see_the_hidden_conversations()
    {
        $user = $this->signIn();
        $participant = create(User::class);
        $hiddenConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $visibleConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $hiddenConversation->hideFrom($participant);
        $this->signIn($participant);

        $conversations = $this->getJson(route('conversations.index'))
            ->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals(
            $visibleConversation->id,
            $conversations[0]['id']
        );
    }

    /** @test */
    public function a_user_will_not_see_the_conversations_that_has_left()
    {
        $user = $this->signIn();
        $participant = create(User::class);
        $leftConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $visibleConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $leftConversation->leftBy($participant);
        $this->signIn($participant);

        $conversations = $this->getJson(route('conversations.index'))
            ->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals(
            $visibleConversation->id,
            $conversations[0]['id']
        );
    }

    /** @test */
    public function get_the_unread_conversations()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $readConversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $this->signIn($participant);
        $participant->read($readConversation);

        $conversations = $this->getJson(
            route('conversations.index', ['unread' => true])
        )->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals($unreadConversation->id, $conversations[0]['id']);
    }

    /** @test */
    public function get_the_conversations_started_by_a_given_username()
    {
        $john = $this->signIn();
        $conversationByJohn = ConversationFactory::by($john)->create();
        $orestis = $this->signIn();
        $conversationByOrestis = ConversationFactory::by($orestis)->create();

        $conversations = $this->getJson(
            route('conversations.index', ['startedBy' => $orestis->name])
        )->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals($conversationByOrestis->id, $conversations[0]['id']);
    }

    /** @test */
    public function get_the_conversations_started_by_multiple_usernames()
    {
        $john = $this->signIn();
        $orestis = create(User::class);
        $conversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$orestis->name])
            ->create();
        $this->signIn($orestis);
        $conversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$john->name])
            ->create();
        $randomUser = $this->signIn();
        $conversationByRandomUser = ConversationFactory::by($randomUser)
            ->withParticipants([$orestis->name, $john->name])
            ->create();
        $this->signIn($orestis);
        $desiredUsernames = "{$orestis->name}, {$john->name}";

        $conversations = $this->getJson(
            route('conversations.index', ['startedBy' => $desiredUsernames])
        )->json()['data'];

        $conversations = collect($conversations);
        $this->assertCount(2, $conversations);
        $conversationIds = $conversations->pluck('id');
        $this->assertContains($conversationByOrestis->id, $conversationIds);
        $this->assertContains($conversationByJohn->id, $conversationIds);
    }

    /** @test */
    public function get_the_conversations_that_are_received_by_a_single_username()
    {
        $conversationStarter = $this->signIn();
        $conversationWithoutOrestis = create(Conversation::class);
        $orestis = create(User::class);
        $conversationWithOrestis = ConversationFactory::withParticipants([$orestis->name])->create();

        $conversations = $this->getJson(
            route('conversations.index', ['receivedBy' => $orestis->name])
        )->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals(
            $conversations[0]['id'],
            $conversationWithOrestis->id
        );
    }

    /** @test */
    public function get_the_conversations_that_are_received_by_multiple_usernames()
    {
        $conversationStarter = $this->signIn();
        $conversationWithoutParticipants = create(Conversation::class);
        $orestis = create(User::class);
        $john = create(User::class);
        $participantNames = "{$orestis->name},{$john->name}";
        $conversationWithParticipants = ConversationFactory::withParticipants(
            [$orestis->name, $john->name]
        )->create();

        $conversations = $this->getJson(
            route('conversations.index', ['receivedBy' => $participantNames])
        )->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals(
            $conversations[0]['id'],
            $conversationWithParticipants->id
        );
    }

    /** @test */
    public function get_the_unread_conversations_that_are_started_by_a_given_username()
    {
        $participant = create(User::class);
        $orestis = $this->signIn();
        $readConversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name])
            ->create();
        $john = $this->signIn();
        $readConversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$participant->name])
            ->create();
        $this->signIn($participant);
        $participant->read($readConversationByJohn);
        $participant->read($readConversationByOrestis);

        $conversations = $this->getJson(
            route(
                'conversations.index',
                ['unread' => true, 'startedBy' => $orestis->name]
            ))->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals($unreadConversationByOrestis->id, $conversations[0]['id']);
    }

    /** @test */
    public function get_the_unread_conversations_that_are_started_by_multiple_usernames()
    {
        $participant = create(User::class);
        $orestis = $this->signIn();
        $readConversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name])
            ->create();
        $john = $this->signIn();
        $readConversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$participant->name])
            ->create();
        $george = $this->signIn();
        $readConversationByGeorge = ConversationFactory::by($george)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByGeorge = ConversationFactory::by($george)
            ->withParticipants([$participant->name])
            ->create();
        $this->signIn($participant);
        $participant->read($readConversationByJohn);
        $participant->read($readConversationByOrestis);
        $participant->read($readConversationByGeorge);
        $desiredUsernames = "{$orestis->name},{$john->name}";

        $conversations = $this->getJson(
            route(
                'conversations.index',
                ['unread' => true, 'startedBy' => $desiredUsernames]
            ))->json()['data'];

        $this->assertCount(2, $conversations);
        $conversationIds = collect($conversations)->pluck('id');
        $this->assertContains($unreadConversationByJohn->id, $conversationIds);
        $this->assertContains($unreadConversationByOrestis->id, $conversationIds);
    }

    /** @test */
    public function get_the_unread_conversations_that_are_received_by_a_given_username()
    {
        $participant = create(User::class);
        $john = create(User::class);
        $george = create(User::class);
        $orestis = $this->signIn();
        $readConversationReceivedByGeorge = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $george->name])
            ->create();
        $unreadConversationReceivedByGeorge = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $george->name])
            ->create();
        $this->signIn($john);
        $readConversationNotReceivedByGeorge = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $unreadConversationNotReceivedByGeorge = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $this->signIn($participant);
        $participant->read($readConversationReceivedByGeorge);
        $participant->read($readConversationNotReceivedByGeorge);

        $conversations = $this->getJson(
            route(
                'conversations.index',
                ['unread' => true, 'receivedBy' => $george->name]
            ))->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals($unreadConversationReceivedByGeorge->id, $conversations[0]['id']);
    }

    /** @test */
    public function get_the_unread_conversations_that_are_received_by_multiple_usernames()
    {
        $participant = create(User::class);
        $john = create(User::class);
        $george = create(User::class);
        $mike = create(User::class);
        $orestis = $this->signIn();
        $readConversationReceivedByGeorge = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $george->name])
            ->create();
        $unreadConversationReceivedByGeorge = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $george->name])
            ->create();
        $readConversationReceivedByMike = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $mike->name])
            ->create();
        $unreadConversationReceivedByMike = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $mike->name])
            ->create();
        $this->signIn($john);
        $readConversationNotReceivedByGeorge = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $unreadConversationNotReceivedByGeorge = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $readConversationNotReceivedByMike = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $unreadConversationNotReceivedByMike = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $this->signIn($participant);
        $participant->read($readConversationReceivedByGeorge);
        $participant->read($readConversationReceivedByMike);
        $participant->read($readConversationNotReceivedByGeorge);
        $participant->read($readConversationNotReceivedByMike);
        $participantNames = "{$george->name}, {$mike->name} ";

        $conversations = $this->getJson(
            route(
                'conversations.index',
                ['unread' => true, 'receivedBy' => $participantNames]
            ))->json()['data'];

        $this->assertCount(2, $conversations);
        $conversationIds = collect($conversations)->pluck('id');
        $this->assertContains($unreadConversationReceivedByGeorge->id, $conversationIds);
        $this->assertContains($unreadConversationReceivedByMike->id, $conversationIds);
    }

    /** @test  */
    public function get_the_recent_and_unread_conversations()
    {
        $conversationStarter = $this->signIn();
        $readAndLastMonthConversation = ConversationFactory::by($conversationStarter)->create();
        $readAndLastMonthConversation->update(
            ['updated_at' => Carbon::now()->subMonth()]
        );
        $unreadLastWeekConversation = ConversationFactory::by($conversationStarter)->create();
        $unreadLastWeekConversation->update(
            ['updated_at' => Carbon::now()->subWeek()]
        );
        $conversationStarter->unread($unreadLastWeekConversation);
        $readAndLastWeekConversation = ConversationFactory::by($conversationStarter)->create();
        $readAndLastWeekConversation->update(
            ['updated_at' => Carbon::now()->subWeek()]
        );
        
        $unreadTodayConversation = ConversationFactory::by($conversationStarter)->create();
        $conversationStarter->unread($unreadTodayConversation);
        $readTodayConversation = ConversationFactory::by($conversationStarter)->create();
        $conversationStarter->read($readAndLastMonthConversation);
        $conversationStarter->read($readAndLastWeekConversation);
        $conversationStarter->read($readTodayConversation);

        $desiredConversations = $this->getJson(
            route('api.conversations.index', ['recentAndUnread' => true])
        )->json();

        $this->assertEquals(
            $desiredConversations[0]['id'],
            $unreadTodayConversation->id
        );
        $this->assertEquals(
            $desiredConversations[1]['id'],
            $unreadLastWeekConversation->id
        );
        $this->assertEquals(
            $desiredConversations[2]['id'],
            $readTodayConversation->id
        );
        $this->assertEquals(
            $desiredConversations[3]['id'],
            $readAndLastWeekConversation->id
        );
        $this->assertCount(4, $desiredConversations);
        $desiredConversationIds = collect($desiredConversations)->pluck('id');
        $this->assertTrue(
            array_key_exists('starter', $desiredConversations[0])
        );
        $this->assertEquals(
            $desiredConversations[0]['starter']['name'],
            $conversationStarter->name
        );
        $this->assertTrue(
            array_key_exists('participants', $desiredConversations[0])
        );
        $this->assertEquals(
            $desiredConversations[0]['participants'][0]['name'],
            $conversationStarter->name
        );
        $this->assertContains(
            $unreadLastWeekConversation->id,
            $desiredConversationIds
        );
        $this->assertContains(
            $readAndLastWeekConversation->id,
            $desiredConversationIds
        );
        $this->assertContains(
            $unreadTodayConversation->id,
            $desiredConversationIds
        );
        $this->assertContains(
            $readTodayConversation->id,
            $desiredConversationIds
        );
    }

    /** @test */
    public function get_the_starred_conversations()
    {
        $conversationStarter = $this->signIn();
        $conversationStarter = $this->signIn();
        $unstarredConversation = create(Conversation::class);
        $starredConversation = create(Conversation::class);
        $starredConversation->star();

        $desiredConversations = $this->getJson(
            route('api.conversations.index', ['starred' => true])
        )->json();

        $this->assertCount(1, $desiredConversations);
        $this->assertEquals(
            $desiredConversations[0]['id'],
            $starredConversation->id
        );
    }
}