<?php

namespace Tests\Browser\Conversations;

use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewDropdownMessagesTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_the_recent_and_unread_messages()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $recentUnreadConversation = ConversationFactory::by($doe)
            ->withParticipants([$john->name])
            ->create();
        $recentReadConversation = ConversationFactory::by($doe)
            ->withParticipants([$john->name])
            ->create();
        $recentReadConversation->read($john);

        $this->browse(function (Browser $browser) use (
            $john,
            $recentUnreadConversation,
            $recentReadConversation,
        ) {
            $browser
                ->loginAs($john)
                ->visit(route('home'))
                ->waitFor('@conversations-button')
                ->click("@conversations-button")
                ->waitFor("@dropdown-conversations")
                ->assertSee($recentUnreadConversation->title)
                ->assertSee($recentReadConversation->title);

        });
    }

    /** @test */
    public function it_doesnt_show_old_read_conversations()
    {
        $john = create(User::class);
        $bob = create(User::class);
        Carbon::setTestNow(now()->subMonth());
        $oldUnreadConversation = ConversationFactory::by($bob)
            ->withParticipants([$john->name])
            ->create();
        $oldReadConversation = ConversationFactory::by($bob)
            ->withParticipants([$john->name])
            ->create();
        $oldReadConversation->read($john);
        Carbon::setTestNow();

        $this->browse(function (Browser $browser) use (
            $john,
            $oldReadConversation,
            $oldUnreadConversation,
        ) {
            $browser
                ->loginAs($john)
                ->visit(route('home'))
                ->waitFor('@conversations-button')
                ->click("@conversations-button")
                ->waitFor("@dropdown-conversations")
                ->assertDontSee($oldReadConversation->title)
                ->assertSee($oldUnreadConversation->title);
        });
    }

    /** @test */
    public function it_shows_the_conversation_participants_of_each_conversation()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $conversationByBob = ConversationFactory::by($bob)
            ->withParticipants([$john->name, $doe->name])
            ->create();
        $conversationByDoe = ConversationFactory::by($doe)
            ->withParticipants([$john->name, $bob->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $john,
            $conversationByBob,
            $conversationByDoe,
        ) {
            $browser
                ->loginAs($john)
                ->visit(route('home'))
                ->waitFor('@conversations-button')
                ->click("@conversations-button")
                ->waitFor("@dropdown-conversations")
                ->assertSee($conversationByBob->title)
                ->waitFor("@dropdown-conversation-{$conversationByBob->id}")
                ->assertSeeIn("@dropdown-conversation-{$conversationByBob->id}", $conversationByBob->participants->first()->name)
                ->assertSeeIn("@dropdown-conversation-{$conversationByBob->id}", $conversationByBob->participants->last()->name)
                ->assertSee($conversationByDoe->title)
                ->waitFor("@dropdown-conversation-{$conversationByDoe->id}")
                ->assertSeeIn("@dropdown-conversation-{$conversationByDoe->id}", $conversationByDoe->participants->first()->name)
                ->assertSeeIn("@dropdown-conversation-{$conversationByDoe->id}", $conversationByDoe->participants->last()->name);
        });
    }

    /** @test */
    public function it_shows_the_date_the_conversation_was_updated()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $conversationByDoe = ConversationFactory::by($doe)
            ->withParticipants([$john->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $john,
            $conversationByDoe,
        ) {
            $browser
                ->loginAs($john)
                ->visit(route('home'))
                ->waitFor('@conversations-button')
                ->click("@conversations-button")
                ->waitFor("@dropdown-conversations")
                ->assertSee($conversationByDoe->title)
                ->waitFor("@dropdown-conversation-{$conversationByDoe->id}")
                ->assertSeeIn("@dropdown-conversation-{$conversationByDoe->id}", $conversationByDoe->updated_at->calendar());
        });
    }

    /** @test */
    public function it_shows_first_the_unread_conversations_and_then_the_most_recently_updated()
    {

    }

    /** @test */
    public function it_shows_conversations_only_by_users_that_are_not_ignored_with_ajax_request()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $conversationByDoe = ConversationFactory::by($doe)->withParticipants([$john->name])->create();
        $conversationByBob = ConversationFactory::by($bob)->withParticipants([$john->name])->create();
        $john->ignore($doe);

        $this->browse(function (Browser $browser) use (
            $john,
            $conversationByDoe,
            $conversationByBob,
        ) {
            $browser
                ->loginAs($john)
                ->visit(route('home'))
                ->waitFor('@conversations-button')
                ->click("@conversations-button")
                ->waitFor("@dropdown-conversations")
                ->assertDontSee($conversationByDoe->title)
                ->assertSee($conversationByBob->title);
        });
    }
}