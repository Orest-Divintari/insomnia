<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Thread;
use App\Models\User;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MentionUsernamesTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $name = 'orestis';
    protected $firstTwoCharacters = 'or';

    /** @test */
    public function users_can_mention_other_users_in_thread_replies()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $mentionedUser = create(User::class, ['name' => $this->name]);

        $this->browse(function (Browser $browser) use ($thread, $user) {
            $browser
                ->loginAs($user)
                ->visit(route('threads.show', $thread))
                ->waitForText($thread->title)
                ->type('@input-reply-wysiwyg', "@{$this->firstTwoCharacters}")
                ->waitForText($this->name)
                ->click('.mention-container > ul > li')
                ->assertSee("@{$this->name}");
        });
    }

    /** @test */
    public function users_can_mention_other_users_in_threads()
    {
        $user = create(User::class);
        $mentionedUser = create(User::class, ['name' => $this->name]);
        $category = create(Category::class);
        $this->browse(function (Browser $browser) use ($category, $user) {
            $browser
                ->loginAs($user)
                ->visit(route('threads.create', $category))
                ->type('@input-reply-wysiwyg', "@{$this->firstTwoCharacters}")
                ->waitForText($this->name)
                ->click('.mention-container > ul > li')
                ->assertSee("@{$this->name}");
        });
    }

    /** @test */
    public function users_can_mention_other_users_in_profile_posts()
    {
        $user = create(User::class);
        $mentionedUser = create(User::class, ['name' => $this->name]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(route('profiles.show', $user))
                ->click("@input-profile-post")
                ->waitFor("@wysiwyg-component")
                ->type('@input-reply-wysiwyg', "@{$this->firstTwoCharacters}")
                ->waitForText($this->name)
                ->click('.mention-container > ul > li')
                ->assertSee("@{$this->name}");
        });
    }

    /** @test */
    public function users_can_mention_other_users_in_comments()
    {
        $user = create(User::class);
        $mentionedUser = create(User::class, ['name' => $this->name]);
        ProfilePostFactory::toProfile($user)->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(route('profiles.show', $user))
                ->click("@input-comment")
                ->waitFor("@wysiwyg-component")
                ->type('@input-reply-wysiwyg', "@{$this->firstTwoCharacters}")
                ->waitForText($this->name)
                ->click('.mention-container > ul > li')
                ->assertSee("@{$this->name}");
        });
    }

    /** @test */
    public function users_can_mention_other_users_in_messages()
    {
        $user = create(User::class);
        $mentionedUser = create(User::class, ['name' => $this->name]);
        $conversation = ConversationFactory::by($user)->withParticipants([$mentionedUser->name])->create();
        $this->browse(function (Browser $browser) use ($user, $conversation) {
            $browser
                ->loginAs($user)
                ->visit(route('conversations.show', $conversation))
                ->waitFor("@wysiwyg-component")
                ->type('@input-reply-wysiwyg', "@{$this->firstTwoCharacters}")
                ->waitForText($this->name)
                ->click('.mention-container > ul > li')
                ->assertSee("@{$this->name}");
        });
    }
}