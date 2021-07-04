<?php

namespace Tests\Browser\Profile;

use App\Activity;
use App\Helpers\ModelType;
use App\Like;
use App\ProfilePost;
use App\Reply;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewLatestActivityTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_a_message_when_profile_owner_does_not_have_any_acivities()
    {
        $user = create(User::class);
        $expectedMessage = "The news feed is currently empty.";
        $this->browse(function (Browser $browser) use ($user, $expectedMessage) {

            $response = $browser->loginAs($user)
                ->visit("/profiles/{$user->name}")
                ->clickLink('Latest Activity')
                ->waitForText($expectedMessage)
                ->assertSee($expectedMessage);
        });
    }

    /** @test */
    public function it_show_the_threads_that_the_profile_has_created()
    {
        $profileOwner = create(User::class);
        $thread = ThreadFactory::by($profileOwner)->create();

        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'type' => ModelType::prefixCreated($thread),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $thread) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText($thread->title);

            $response
                ->assertSee($thread->title)
                ->assertSee($thread->body)
                ->assertSee($thread->date_created)
                ->assertSee("thread by")
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee($thread->category->title);
        });
    }

    /** @test */
    public function it_shows_the_thread_replies_that_profile_owner_has_created()
    {
        $profileOwner = create(User::class);
        $reply = ReplyFactory::by($profileOwner)->create();
        $thread = $reply->repliable;
        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $reply->id,
            'subject_type' => Reply::class,
            'type' => ModelType::prefixCreated($reply),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $reply, $thread) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText($thread->title);

            $response
                ->assertSee($thread->title)
                ->assertSee($reply->body)
                ->assertSee($reply->date_created)
                ->assertSee('reply by')
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee($thread->category->title);
        });
    }

    /** @test */
    public function it_shows_the_profile_posts_that_profile_owner_has_created_on_other_user_profile()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::by($profileOwner)->create();

        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => ModelType::prefixCreated($profilePost),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $profilePost) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText($profilePost->body);

            $response
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee("left a message on")
                ->assertSeeIn('@latest-activity-tab', $profilePost->profileOwner->name)
                ->assertSee($profilePost->body)
                ->assertSee($profilePost->date_created);
        });
    }

    /** @test */
    public function it_shows_the_profile_post_that_profile_owners_have_created_on_their_own_profile()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::by($profileOwner)
            ->toProfile($profileOwner)
            ->create();

        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $profilePost->id,
            'subject_type' => ProfilePost::class,
            'type' => ModelType::prefixCreated($profilePost),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $profilePost) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText($profilePost->body);

            $response
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee("updated their status")
                ->assertSee($profilePost->body)
                ->assertSee($profilePost->date_created);
        });
    }

    /** @test */
    public function it_shows_the_profile_post_comments_that_profile_owner_has_created()
    {
        $profileOwner = create(User::class);
        $comment = CommentFactory::by($profileOwner)->create();

        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $comment->id,
            'subject_type' => Reply::class,
            'type' => ModelType::prefixCreated($comment),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $comment) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText($comment->body);

            $response
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee('commented on')
                ->assertSeeIn('@latest-activity-tab', "{$comment->repliable->poster->name}'s profile post")
                ->assertSee($comment->body)
                ->assertSee($comment->date_created);
        });
    }

    /** @test */
    public function it_shows_the_profile_posts_that_profile_owner_has_liked()
    {
        $profileOwner = create(User::class);
        $profilePost = ProfilePostFactory::by($profileOwner)->create();
        $like = $profilePost->like($profileOwner);

        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => ModelType::prefixCreated($like),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $profilePost, $like) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText('liked');

            $response
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee("liked {$profilePost->poster->name}'s post")
                ->assertSee("on {$profilePost->profileOwner->name}'s profile")
                ->assertSee($like->date_created);
        });
    }

    /** @test */
    public function it_shows_the_profile_post_comments_that_profile_owner_has_liked()
    {
        $profileOwner = create(User::class);
        $comment = CommentFactory::by($profileOwner)->create();
        $like = $comment->like($profileOwner);

        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => ModelType::prefixCreated($like),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $comment, $like) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText('liked');

            $response
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee("liked {$comment->poster->name}'s comment")
                ->assertSee("on {$comment->repliable->poster->name}'s profile post")
                ->assertSee($like->date_created);
        });
    }

    /** @test */
    public function it_shows_the_thread_replies_that_profile_owner_has_liked()
    {
        $profileOwner = create(User::class);
        $reply = ReplyFactory::create();
        $like = $reply->like($profileOwner);
        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $like->id,
            'subject_type' => Like::class,
            'type' => ModelType::prefixCreated($like),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $reply, $like) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Latest Activity')
                ->waitForText('liked');

            $response
                ->assertSeeIn('@latest-activity-tab', $profileOwner->name)
                ->assertSee("liked {$reply->poster->name}'s post")
                ->assertSee("in the thread {$reply->repliable->title}")
                ->assertSee($like->date_created);
        });
    }

    /** @test */
    public function visitors_may_view_more_of_profile_owners_latest_activities()
    {
        $profileOwner = create(User::class);
        $threads = ThreadFactory::by($profileOwner)
            ->createMany(Activity::NUMBER_OF_ACTIVITIES * 2);
        $threads->each(function ($thread) use ($profileOwner) {
            Activity::create([
                'user_id' => $profileOwner->id,
                'subject_id' => $thread->id,
                'subject_type' => Thread::class,
                'type' => ModelType::prefixCreated($thread),
            ]);
        });

        $this->browse(function (Browser $browser) use ($profileOwner, $threads) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner) . "?")
                ->clickLink('Latest Activity')
                ->waitFor('@fetch-more-button')
                ->click('@fetch-more-button');

            $threads->each(function ($thread) use ($response) {
                $response->assertSee($thread->title);
            });
        });

    }

}
