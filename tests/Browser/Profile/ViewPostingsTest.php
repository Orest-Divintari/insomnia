<?php

namespace Tests\Browser\Profile;

use App\Activity;
use App\Helpers\ModelType;
use App\ProfilePost;
use App\Reply;
use App\Tag;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewPostingsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_a_message_when_profile_owner_does_not_have_any_posting_acivities()
    {
        $user = create(User::class);
        $expectedMessage = "{$user->name} has not posted any content recently.";
        $this->browse(function (Browser $browser) use ($user, $expectedMessage) {

            $response = $browser->loginAs($user)
                ->visit("/profiles/{$user->name}")
                ->clickLink('Postings')
                ->waitForText($expectedMessage)
                ->assertSee($expectedMessage);
        });
    }

    /** @test */
    public function it_shows_the_threads_that_profile_owner_has_created()
    {
        $profileOwner = create(User::class);
        $tag = create(Tag::class);
        $thread = ThreadFactory::by($profileOwner)->create();
        $thread->addTags([$tag->name]);
        Activity::create([
            'user_id' => $profileOwner->id,
            'subject_id' => $thread->id,
            'subject_type' => Thread::class,
            'type' => ModelType::prefixCreated($thread),
        ]);

        $this->browse(function (Browser $browser) use ($profileOwner, $tag, $thread) {
            $response = $browser
                ->loginAs($profileOwner)
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Postings')
                ->waitForText($thread->title);

            $response
                ->assertSeeIn('@postings-tab', $profileOwner->name)
                ->assertSee($thread->title)
                ->assertSee($thread->body)
                ->assertSee('Thread')
                ->assertSee($thread->date_created)
                ->assertSee("Category: {$thread->category->title}")
                ->assertSee("Replies: {$thread->replies_count}")
                ->assertSee($tag->name);
        });
    }

    /** @test */
    public function it_shows_the_thread_replies_that_profile_owner_has_created()
    {
        $profileOwner = create(User::class);
        $tag = create(Tag::class);
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
                ->clickLink('Postings')
                ->waitForText($thread->title);

            $response
                ->assertSeeIn('@postings-tab', $profileOwner->name)
                ->assertSee($thread->title)
                ->assertSee($reply->body)
                ->assertSee("Post #{$reply->position}")
                ->assertSee($reply->date_created)
                ->assertSee("Category: {$thread->category->title}");

        });
    }

    /** @test */
    public function it_shows_the_profile_posts_that_profile_owner_has_created()
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
                ->clickLink('Postings')
                ->waitForText($profilePost->body);

            $response
                ->assertSeeIn('@postings-tab', $profileOwner->name)
                ->assertSee($profilePost->body)
                ->assertSee('Profile post')
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
                ->clickLink('Postings')
                ->waitForText($comment->body);

            $response
                ->assertSeeIn('@postings-tab', $profileOwner->name)
                ->assertSee($comment->body)
                ->assertSee('Profile post comment')
                ->assertSee($comment->date_created);
        });
    }

    /** @test */
    public function visitors_may_view_more_of_profile_owners_posting_activities()
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
                ->visit(route('profiles.show', $profileOwner))
                ->clickLink('Postings')
                ->waitFor('@fetch-more-button')
                ->click('@fetch-more-button');

            $threads->each(function ($thread) use ($response) {
                $response->assertSee($thread->title);
            });
        });
    }
}