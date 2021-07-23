<?php

namespace Tests\Unit;

use App\Filters\ConversationFilters;
use App\Models\Conversation;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationFiltersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The instance of conversation filters
     *
     * @var ConversationFilters
     */
    protected $conversationFilters;

    public function setUp(): void
    {
        parent::setUp();
        $this->conversationFilters = new ConversationFilters();
        $this->conversationFilters->setBuilder(Conversation::query());
    }

    /** @test */
    public function get_the_unread_conversations()
    {
        $conversationStarter = $this->signIn();
        $readConversation = create(Conversation::class);
        $unreadConversation = create(Conversation::class);
        $readConversation->read($conversationStarter);

        $conversations = $this->conversationFilters->unread()->get();

        $this->assertCount(1, $conversations);
        $this->assertEquals($unreadConversation->id, $conversations->first()->id);
    }

    /** @test */
    public function get_the_conversations_that_are_started_by_a_given_username()
    {
        $john = $this->signIn();
        $conversationByJohn = ConversationFactory::by($john)->create();
        $orestis = $this->signIn();
        $conversationByOrestis = ConversationFactory::by($orestis)->create();

        $conversation = $this->conversationFilters
            ->startedBy($orestis->name)
            ->get();

        $this->assertCount(1, $conversation);
        $this->assertEquals(
            $conversationByOrestis->id,
            $conversation->first()->id
        );
    }

    /** @test */
    public function get_the_conversations_that_are_started_by_multiple_given_usernames()
    {
        $john = $this->signIn();
        $conversationByJohn = ConversationFactory::by($john)->create();
        $orestis = $this->signIn();
        $conversationByOrestis = ConversationFactory::by($orestis)->create();
        $randomUser = $this->signIn();
        $conversationByRandomUser = ConversationFactory::by($randomUser)->create();
        $desiredUsernames = "{$orestis->name}, {$john->name}";

        $conversations = $this->conversationFilters
            ->startedBy($desiredUsernames)
            ->get();

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

        $conversation = $this->conversationFilters
            ->receivedBy($orestis->name)
            ->get();

        $this->assertCount(1, $conversation);
        $this->assertEquals(
            $conversation->first()->id,
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

        $conversation = $this->conversationFilters
            ->receivedBy($participantNames)
            ->get();

        $this->assertCount(1, $conversation);
        $this->assertEquals(
            $conversation->first()->id,
            $conversationWithParticipants->id
        );
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
        $readAndLastWeekConversation = ConversationFactory::by($conversationStarter)->create();
        $readAndLastWeekConversation->update(
            ['updated_at' => Carbon::now()->subWeek()]
        );
        $unreadTodayConversation = ConversationFactory::by($conversationStarter)->create();
        $readTodayConversation = ConversationFactory::by($conversationStarter)->create();
        $readAndLastMonthConversation->read($conversationStarter);
        $readAndLastWeekConversation->read($conversationStarter);
        $readTodayConversation->read($conversationStarter);

        $desiredConversations = $this->conversationFilters
            ->recentAndUnread()
            ->get();

        $this->assertCount(4, $desiredConversations);
        $desiredConversationIds = collect($desiredConversations)->pluck('id');
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
        $unstarredConversation = create(Conversation::class);
        $starredConversation = create(Conversation::class);

        $starredConversation->starredBy();

        $conversations = $this->conversationFilters->starred()->get();
        $this->assertCount(1, $conversations);
        $this->assertEquals($conversations->first()->id, $starredConversation->id);
        $this->assertTrue($conversations->first()->starred());
    }
}
