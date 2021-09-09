<?php

namespace Tests\Browser\Conversations;

use App\Models\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InteractWithConversationTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function a_user_can_like_a_message()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();
        $message = $conversation->messages()->first();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $participant,
            $message,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@like-button')
                ->pause(500)
                ->assertVue('isLiked', true, '@like-button-component');
        });
    }

    /** @test */
    public function users_may_star_a_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@star-conversation-button')
                ->waitForText('Unstar')
                ->assertSee('Unstar')
                ->refresh()
                ->assertSee('Unstar');

            $browser->visit(route('conversations.index'))
                ->assertPresent('@conversation-star');
        });
    }

    /** @test */
    public function users_may_unstar_a_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $conversation->starredBy($conversationStarter);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@unstar-conversation-button')
                ->waitForText('Star')
                ->assertSee('Star')
                ->refresh()
                ->assertSee('Star');

            $browser->visit(route('conversations.index'))
                ->assertMissing('@conversation-star');
        });
    }

    /** @test */
    public function users_may_unread_a_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@unread-conversation-button')
                ->waitForText('Mark read')
                ->assertSee('Mark read');
        });
    }

    /** @test */
    public function users_may_mark_a_conversation_as_read()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->assertSee('Mark unread')
                ->click('@unread-conversation-button')
                ->waitForText('Mark read')
                ->assertSee('Mark read')
                ->click('@read-conversation-button')
                ->waitForText('Mark unread')
                ->assertSee('Mark unread');
        });
    }

    /** @test */
    public function the_conversation_starter_may_edit_the_title_of_the_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $newTitle = $this->faker()->sentence();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation,
            $newTitle
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@edit-conversation-button')
                ->assertVisible('@edit-conversation-modal')
                ->type('@conversation-title-input', $newTitle)
                ->click('@save-conversation-button')
                ->assertNotPresent('@edit-conversatione-modal')
                ->assertSee($newTitle);
        });
    }

    /** @test */
    public function the_conversation_starter_may_lock_the_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@edit-conversation-button')
                ->assertVisible('@edit-conversation-modal')
                ->waitFor('@lock-conversation-checkbox')
                ->check('@lock-conversation-checkbox')
                ->assertChecked('@lock-conversation-checkbox')
                ->click('@save-conversation-button')
                ->assertNotPresent('@edit-conversatione-modal')
                ->waitForText('Closed for new replies.')
                ->assertSee("Closed for new replies.");
        });
    }

    /** @test */
    public function the_conversation_starter_may_unlock_the_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $conversation->lock();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->assertSee('Closed for new replies.')
                ->click('@edit-conversation-button')
                ->assertVisible('@edit-conversation-modal')
                ->assertChecked('@lock-conversation-checkbox')
                ->uncheck('@lock-conversation-checkbox')
                ->click('@save-conversation-button')
                ->assertNotPresent('@edit-conversatione-modal')
                ->waitUntilMissingText('Closed for new replies.')
                ->assertDontSee("Closed for new replies.");
        });
    }

    /** @test */
    public function a_participant_may_leave_a_conversation_and_allow_future_messages_to_appear()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@leave-conversation-button')
                ->assertVisible('@leave-conversation-modal')
                ->waitFor('@allow-future-messages-checkbox')
                ->assertChecked('@allow-future-messages-checkbox')
                ->click('@leave-conversation-submit')
                ->waitForText('There are no conversations to display.')
                ->assertSee('There are no conversations to display.');
        });
    }

    /** @test */
    public function a_participant_may_leave_a_conversation_and_ignore_future_messages_to_appear()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@leave-conversation-button')
                ->assertVisible('@leave-conversation-modal')
                ->waitFor('@ignore-future-messages-checkbox')
                ->check('@ignore-future-messages-checkbox')
                ->assertChecked('@ignore-future-messages-checkbox')
                ->click('@leave-conversation-submit')
                ->waitForText('There are no conversations to display.')
                ->assertSee('There are no conversations to display.');
        });
    }

    /** @test */
    public function a_conversation_admin_can_set_another_participant_as_admin()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->waitFor('@participant-settings-button')
                ->click('@participant-settings-button')
                ->assertVisible('@participant-settings')
                ->click('@set-participant-as-admin-button')
                ->assertMissing('@participant-settings')
                ->click('@participant-settings-button')
                ->waitFor('@remove-participant-as-admin-button')
                ->assertVisible('@remove-participant-as-admin-button')
                ->refresh()
                ->click('@participant-settings-button')
                ->waitFor('@remove-participant-as-admin-button')
                ->assertVisible('@remove-participant-as-admin-button');
        });
    }

    /** @test */
    public function a_conversation_admin_can_remove_another_participant_as_admin()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $conversation->setAdmin($participant->id);

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@participant-settings-button')
                ->assertVisible('@participant-settings')
                ->click('@remove-participant-as-admin-button')
                ->assertMissing('@participant-settings')
                ->click('@participant-settings-button')
                ->waitFor('@set-participant-as-admin-button')
                ->assertVisible('@set-participant-as-admin-button')
                ->refresh()
                ->click('@participant-settings-button')
                ->assertVisible('@set-participant-as-admin-button');
        });
    }

    /** @test */
    public function a_conversation_admin_can_remove_a_participant_from_the_conversation()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation,
            $participant
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@participant-settings-button')
                ->assertVisible('@participant-settings')
                ->click('@remove-participant-button')
                ->waitForReload()
                ->assertDontSee($participant->name);
        });
    }

    /** @test */
    public function a_conversation_admin_may_invite_other_participants_in_the_conversation()
    {
        $conversationStarter = create(User::class);
        $john = create(User::class);
        $doe = create(User::class);
        $george = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$john->name])
            ->create();
        $participantNames = "{$doe->name}, {$george->name}";
        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation,
            $doe,
            $george,
            $participantNames
        ) {

            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@invite-participants-button')
                ->waitFor('@invite-participants-modal')
                ->assertVisible('@invite-participants-modal')
                ->assertVisible('#participants')
                ->type('#participants', $participantNames)
                ->waitFor('@invite-participants-submit')
                ->click('@invite-participants-submit')
                ->waitForReload()
                ->assertSee($doe->name)
                ->assertSee($george->name);
        });
    }

    /** @test */
    public function users_may_go_back_to_the_list_of_all_conversations()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();
        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->clickLink('Conversations')
                ->assertRouteIs('conversations.index');

        });
    }

    /** @test */
    public function users_may_visit_the_profile_of_the_user_who_sent_the_last_message()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->create();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@last-conversation-message-poster')
                ->assertRouteIs('profiles.show', $conversationStarter);
        });
    }

}