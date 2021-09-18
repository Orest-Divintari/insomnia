<?php

namespace Tests\Browser\Conversations;

use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewConversationsTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function user_can_view_the_converastions_that_participates_in()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage('some message')
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversation,
            $conversationStarter,
            $participant,
            $message
        ) {
            $browser->loginAs($conversationStarter)
                ->visit('/conversations')
                ->assertSee('Conversations')
                ->assertSee('Start Conversation')
                ->assertSee('Filters')
                ->assertMissing('@conversation-star')
                ->assertAttribute('img', 'src', $conversationStarter->avatar_path)
                ->assertSeeLink($conversation->title)
                ->assertSeeIn('#conversation-' . $conversation->id . '-participants', $conversationStarter->name)
                ->assertSeeIn('#conversation-' . $conversation->id . '-participants', $participant->name)
                ->assertSeeIn('#conversation-' . $conversation->id . '-date-created', $conversation->dateCreated)
                ->assertSee('Replies:')
                ->assertSeeIn('#conversation-' . $conversation->id . '-messages-count', $conversation->messages()->count())
                ->assertSee('Participants:')
                ->assertSeeIn('#conversation-' . $conversation->id . '-participants-count', $conversation->participants()->count())
                ->assertSeeIn('#conversation-' . $conversation->id . '-date-updated', $conversation->dateUpdated)
                ->assertSeeIn('#conversation-' . $conversation->id . '-recent-poster', $conversationStarter->name);
        });

    }

    /** @test */
    public function it_shows_a_message_when_there_are_no_conversations_to_display()
    {
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                ->visit('/conversations')
                ->assertSee('There are no conversations to display.');
        });
    }

    /** @test */
    public function a_user_can_view_only_the_unread_conversations()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $readConversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage('some message')
            ->create();
        $unreadConversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage('some message')
            ->create();

        $readConversation->read($conversationStarter);
        $unreadConversation->unread($conversationStarter);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $unreadConversation,
            $readConversation
        ) {
            $browser->loginAs($conversationStarter)
                ->visit(route('conversations.index'))
                ->click('@conversation-filters-dropdown')
                ->assertSee('Unread')
                ->check('@unread-filter')
                ->click('@conversation-filters-button')
                ->assertSee('Show only: Unread')
                ->assertSeeLink($unreadConversation->title)
                ->assertDontSeeLink($readConversation->title);
        });
    }

    /** @test */
    public function a_user_can_view_only_the_starred_conversations()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $starredConversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $starredConversation->starredBy($conversationStarter);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $starredConversation,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.index'))
                ->click('@conversation-filters-dropdown')
                ->assertSee('Starred')
                ->assertVisible('@conversation-star')
                ->check('@starred-filter')
                ->click('@conversation-filters-button')
                ->assertSee('Show only: Starred')
                ->assertSeeLink($starredConversation->title)
                ->assertDontSeeLink($conversation->title);
        });
    }

    /** @test */
    public function a_user_can_view_the_conversations_started_by_a_given_username()
    {
        $john = create(User::class);
        $conversationByJohn = ConversationFactory::by($john)->create();
        $orestis = create(User::class);
        $conversationByOrestis = ConversationFactory::by($orestis)->create();

        $this->browse(function (Browser $browser) use (
            $orestis,
            $conversationByOrestis,
            $conversationByJohn
        ) {
            $browser
                ->loginAs($orestis)
                ->visit(route('conversations.index'))
                ->click('@conversation-filters-dropdown')
                ->assertSee('Started By')
                ->type('#started-by-conversation-filter', $orestis->name)
                ->click('@conversation-filters-button')
                ->assertSee('Started by: ' . $orestis->name)
                ->assertSeeLink($conversationByOrestis->title)
                ->assertDontSeeLink($conversationByJohn->title);
        });
    }

    /** @test */
    public function a_user_can_view_the_conversations_that_a_given_user_has_received()
    {
        $john = create(User::class);
        $conversationByJohn = ConversationFactory::by($john)->create();
        $orestis = create(User::class);
        $george = create(User::class);
        $conversationReceivedByGoerge = ConversationFactory::by($orestis)
            ->withParticipants([$george->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationReceivedByGoerge,
            $conversationByJohn,
            $george,
            $orestis
        ) {
            $browser
                ->loginAs($orestis)
                ->visit(route('conversations.index'))
                ->click('@conversation-filters-dropdown')
                ->assertSee('Received By')
                ->type('#received-by-conversation-filter', $george->name)
                ->click('@conversation-filters-button')
                ->assertSee('Received by: ' . $george->name)
                ->assertSeeLink($conversationReceivedByGoerge->title)
                ->assertDontSeeLink($conversationByJohn->title);
        });
    }

    /** @test */
    public function a_user_can_view_only_the_recent_and_unread_conversations()
    {
        $conversationStarter = create(User::class);
        $readLastMonthConversation = ConversationFactory::by($conversationStarter)->create();
        $readLastMonthConversation->update(
            ['updated_at' => Carbon::now()->subMonth()]
        );
        $unreadLastWeekConversation = ConversationFactory::by($conversationStarter)->create();
        $unreadLastWeekConversation->update(
            ['updated_at' => Carbon::now()->subWeek()]
        );
        $unreadLastWeekConversation->unread($conversationStarter);
        $readLastWeekConversation = ConversationFactory::by($conversationStarter)->create();
        $readLastWeekConversation->update(
            ['updated_at' => Carbon::now()->subWeek()]
        );

        $unreadTodayConversation = ConversationFactory::by($conversationStarter)->create();
        $unreadTodayConversation->unread($conversationStarter);
        $readTodayConversation = ConversationFactory::by($conversationStarter)->create();
        $readLastMonthConversation->read($conversationStarter);
        $readLastWeekConversation->read($conversationStarter);
        $readTodayConversation->read($conversationStarter);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $readLastWeekConversation,
            $readTodayConversation,
            $unreadTodayConversation,
            $unreadLastWeekConversation,
            $readLastMonthConversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.index', ['recent_and_unread' => true]))
                ->assertSeeLink($unreadLastWeekConversation->title)
                ->assertSeeLink($unreadTodayConversation->title)
                ->assertSeeLink($readTodayConversation->title)
                ->assertSeeLink($readLastWeekConversation->title)
                ->assertDontSeeLink($readLastMonthConversation->title);
        });
    }

    /** @test */
    public function a_user_can_view_only_the_visible_conversations()
    {
        $user = create(User::class);
        $participant = create(User::class);
        $hiddenConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $visibleConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $hiddenConversation->hideFrom($participant);

        $this->browse(function (Browser $browser) use (
            $participant,
            $hiddenConversation,
            $visibleConversation
        ) {
            $browser
                ->loginAs($participant)
                ->visit(route('conversations.index'))
                ->assertSeeLink($visibleConversation->title)
                ->assertDontSeeLink($hiddenConversation->title);
        });
    }

    /** @test */
    public function a_user_can_view_only_the_participating_conversations()
    {
        $user = create(User::class);
        $participant = create(User::class);
        $leftConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $visibleConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();
        $leftConversation->leftBy($participant);

        $this->browse(function (Browser $browser) use (
            $participant,
            $leftConversation,
            $visibleConversation
        ) {
            $browser
                ->loginAs($participant)
                ->visit(route('conversations.index'))
                ->assertSeeLink($visibleConversation->title)
                ->assertDontSeeLink($leftConversation->title);
        });
    }

    /** @test */
    public function a_user_can_view_the_unread_conversations_that_are_started_by_a_given_username()
    {
        $participant = create(User::class);
        $orestis = create(User::class);
        $readConversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByOrestis = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name])
            ->create();
        $john = create(User::class);
        $readConversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$participant->name])
            ->create();
        $unreadConversationByJohn = ConversationFactory::by($john)
            ->withParticipants([$participant->name])
            ->create();
        $readConversationByJohn->read($participant);
        $readConversationByOrestis->read($participant);

        $this->browse(function (Browser $browser) use (
            $participant,
            $unreadConversationByOrestis,
            $orestis,
            $readConversationByJohn,
            $unreadConversationByJohn,
            $readConversationByOrestis
        ) {
            $browser
                ->loginAs($participant)
                ->visit(route('conversations.index'))
                ->click('@conversation-filters-dropdown')
                ->click('@unread-filter')
                ->type('#started-by-conversation-filter', $orestis->name)
                ->click('@conversation-filters-button')
                ->assertSeeLink($unreadConversationByOrestis->title)
                ->assertDontSeeLink($readConversationByJohn->title)
                ->assertDontSeeLink($unreadConversationByJohn->title)
                ->assertDontSeeLink($readConversationByOrestis->title);
        });
    }

    /** @test */
    public function a_user_can_view_the_unread_conversations_that_a_given_user_has_received()
    {
        $participant = create(User::class);
        $john = create(User::class);
        $george = create(User::class);
        $orestis = create(User::class);
        $readConversationReceivedByGeorge = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $george->name])
            ->create();
        $unreadConversationReceivedByGeorge = ConversationFactory::by($orestis)
            ->withParticipants([$participant->name, $john->name, $george->name])
            ->create();
        $readConversationNotReceivedByGeorge = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $unreadConversationNotReceivedByGeorge = ConversationFactory::by($john)
            ->withParticipants([$participant->name, $orestis->name])
            ->create();
        $readConversationReceivedByGeorge->read($participant);
        $readConversationNotReceivedByGeorge->read($participant);

        $this->browse(function (Browser $browser) use (
            $participant,
            $readConversationReceivedByGeorge,
            $unreadConversationReceivedByGeorge,
            $george,
            $readConversationNotReceivedByGeorge,
            $unreadConversationNotReceivedByGeorge
        ) {
            $browser
                ->loginAs($participant)
                ->visit(route('conversations.index'))
                ->click('@conversation-filters-dropdown')
                ->click('@unread-filter')
                ->type('#received-by-conversation-filter', $george->name)
                ->click('@conversation-filters-button')
                ->assertSeeLink($unreadConversationReceivedByGeorge->title)
                ->assertDontSeeLink($readConversationReceivedByGeorge->title)
                ->assertDontSeeLink($readConversationNotReceivedByGeorge->title)
                ->assertDontSeeLink($unreadConversationNotReceivedByGeorge->title);
        });
    }

    /** @test */
    public function a_user_can_view_a_conversation_and_the_associated_messages()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $participant,
            $message,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->assertSee($message['body'])
                ->assertSee('Star')
                ->assertSee('Mark unread')
                ->assertDontSee('likes')
                ->assertSeeIn('@conversation-info', 'Replies')
                ->assertSeeIn('@conversation-info', $conversation->messages->count())
                ->assertSeeIn('@conversation-info', 'Participants')
                ->assertSeeIn('@conversation-info', $conversation->participants->count())
                ->assertSeeIn('@conversation-info', 'Last reply date')
                ->assertSeeIn('@conversation-info', $conversation->messages->last()->dateCreated)
                ->assertSeeIn('@conversation-participants', $participant->name)
                ->assertSeeIn('@conversation-participants', $conversationStarter->name)
                ->assertSeeIn('@conversation-info', 'Last reply from:')
                ->assertSeeIn('@conversation-info', $conversationStarter->name);
        });
    }

    /** @test */
    public function participants_should_not_see_the_edit_conversation_button()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $participant,
            $conversation
        ) {
            $browser
                ->loginAs($participant)
                ->visit(route('conversations.show', $conversation))
                ->assertMissing('@edit-conversation-button');
        });

    }

    /** @test */
    public function non_conversation_admin_participants_should_not_see_the_conversation_settings_button()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $participant,
            $conversation
        ) {
            $browser
                ->loginAs($participant)
                ->visit(route('conversations.show', $conversation))
                ->assertMissing('@participant-settings-button');
        });
    }

    /** @test */
    public function it_shows_the_number_of_likes_a_message_has()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();
        $message = $conversation->messages()->first();
        $message->like($conversationStarter);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $participant,
            $message,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->assertSee('1 likes');
        });
    }

    /** @test */
    public function it_shows_whether_a_message_has_been_liked_by_the_visitor()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();
        $message = $conversation->messages()->first();
        $message->like($conversationStarter);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $participant,
            $message,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->assertVue('isLiked', true, '@like-button-component');
        });
    }

    /** @test */
    public function the_authenticated_user_should_not_see_the_messages_that_are_sent_by_ignored_users()
    {
        $conversationStarter = create(User::class);
        $john = create(User::class);
        $doe = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$john->name, $doe->name])
            ->withMessage($message['body'])
            ->create();
        $ignoredMessage = $conversation->addMessage(['body' => $this->faker()->sentence()], $john);
        $conversationStarter->ignore($john);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $john,
            $ignoredMessage,
            $conversation
        ) {
            $response = $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation));

            $response
                ->assertVisible('@show-ignored-content-button')
                ->assertDontSee($ignoredMessage->body);
        });

    }

    /** @test */
    public function the_authenticated_user_can_reveal_the_messages_that_are_sent_by_ignored_users()
    {
        $conversationStarter = create(User::class);
        $john = create(User::class);
        $doe = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$john->name, $doe->name])
            ->withMessage($message['body'])
            ->create();
        $ignoredMessage = $conversation->addMessage(['body' => $this->faker()->sentence()], $john);
        $conversationStarter->ignore($john);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $john,
            $ignoredMessage,
            $conversation
        ) {
            $response = $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation));

            $response
                ->assertVisible('@show-ignored-content-button')
                ->assertDontSee($ignoredMessage->body)
                ->click('@show-ignored-content-button')
                ->assertSee($ignoredMessage->body);;
        });
    }

    /** @test */
    public function the_authenticated_user_should_not_see_the_conversations_that_are_started_by_ignored_users()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $ignoredConversation = ConversationFactory::by($john)
            ->withParticipants([$doe->name])
            ->withMessage('some message')
            ->create();
        $doe->ignore($john);

        $this->browse(function (Browser $browser) use ($ignoredConversation, $doe) {
            $response = $browser->loginAs($doe)
                ->visit(route('conversations.index'));

            $response->assertDontSee($ignoredConversation->title);
        });
    }
}