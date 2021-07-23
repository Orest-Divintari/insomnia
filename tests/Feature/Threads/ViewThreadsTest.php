<?php

namespace Tests\Feature\Threads;

use App\Models\Category;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewThreadsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_view_a_thread()
    {
        $thread = ThreadFactory::create();

        $response = $this->get(route('threads.show', $thread));

        $response->assertSee($thread->title);
    }

    /** @test */
    public function it_returns_all_thread_replies_including_thread_replies_created_by_ignored_users()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $bob = create(User::class);
        $thread = ThreadFactory::by($john)->create();
        $replyByDoe = ReplyFactory::by($doe)->toThread($thread)->create();
        $john->ignore($doe);
        ReplyFactory::by($bob)->toThread($thread)->create();

        $response = $this->get(route('threads.show', $thread));

        $replies = collect($response['replies']['data']);
        $ignoredReply = $replies->firstWhere('id', $replyByDoe->id);
        $this->assertTrue($ignoredReply['creator_ignored_by_visitor']);
        $unignoredReplies = $replies->filter(function ($reply) use ($ignoredReply) {
            return $reply['id'] != $ignoredReply['id'];
        });
        $this->assertTrue(
            $unignoredReplies->every(function ($reply) {
                return !$reply['creator_ignored_by_visitor'];
            })
        );
    }

    /** @test */
    public function it_returns_threads_that_are_created_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $threadByDoe = ThreadFactory::by($doe)->create();
        $threadByBob = ThreadFactory::by($bob)->create();
        $john->ignore($doe);
        $this->signIn($john);

        $response = $this->get(route('threads.index'));

        $threads = collect($response['threads']->items());
        $this->assertCount(1, $threads);
        $this->assertFalse($threads->search(function ($thread) use ($threadByDoe) {
            return $thread->id == $threadByDoe->id;
        }));
    }

    /** @test */
    public function it_returns_the_threads_that_are_not_marked_as_ignored()
    {
        $john = create(User::class);
        $ignoredThread = create(Thread::class);
        $unignoredThread = create(Thread::class);
        $john->ignore($ignoredThread);
        $this->signIn($john);

        $response = $this->get(route('threads.index'));

        $threads = collect($response['threads']->items());
        $this->assertCount(1, $threads);
        $this->assertFalse($threads->search(function ($thread) use ($ignoredThread) {
            return $thread->id == $ignoredThread->id;
        }));
    }

    /** @test */
    public function a_user_can_view_the_threads_associated_with_a_category()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();

        $response = $this->get(route('category-threads.index', $category));

        $response->assertSee($thread->title);
    }

    /** @test */
    public function a_user_can_view_the_paginated_threads_associated_with_a_category()
    {
        $category = create(Category::class);
        ThreadFactory::inCategory($category)->createMany(100);

        $firstPage = $this->getJson(route('ajax.threads.index', $category))->json();
        $secondPage = $this->getJson(route('ajax.threads.index', $category) . '?page=2')->json();

        $this->assertCount(Thread::PER_PAGE, $firstPage['data']);
        $this->assertCount(Thread::PER_PAGE, $secondPage['data']);
    }

    /** @test */
    public function a_user_can_jump_to_a_specific_reply_of_a_thread()
    {
        $this->withoutExceptionHandling();
        $thread = ThreadFactory::create();
        ReplyFactory::toThread($thread)->createMany(30);
        $desiredReply = Reply::find(15);
        $numberOfPreviousReplies = $thread->replies()
            ->where('id', '<=', $desiredReply->id)
            ->count();
        $pageNumber = (int) ceil($numberOfPreviousReplies / Thread::REPLIES_PER_PAGE);

        $response = $this->get(route('replies.show', $desiredReply));

        $response->assertSee($desiredReply->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_username()
    {
        $uric = create(User::class);
        $john = create(User::class);
        $threadByUric = ThreadFactory::by($uric)->create();
        $threadByJohn = ThreadFactory::by($john)->create();

        $response = $this->get(route('threads.index') . "?posted_by={$uric->name}");

        $response->assertSee($threadByUric->title)
            ->assertDontSee($threadByJohn->title);
    }

    /** @test */
    public function a_user_can_filter_the_threads_of_a_category_by_username()
    {
        $category = create(Category::class);
        $uric = create(User::class);
        $threadByUric = ThreadFactory::by($uric)
            ->inCategory($category)
            ->create();
        $john = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->inCategory($category)
            ->create();

        $response = $this->get(route(
            'category-threads.index',
            [$category, 'posted_by' => $uric->name]
        ));

        $response->assertSee($threadByUric->title)
            ->assertDontSee($threadByJohn->title);
    }

    /** @test */
    public function user_can_view_the_threads_ordered_by_date_of_creation_in_descending_order()
    {
        $newThread = create(Thread::class);
        $oldThread = ThreadFactory::createdAt(Carbon::now()->subDay())->create();

        $response = $this->get(route('threads.index', ['new_threads' => true]));

        $response->assertSeeInOrder([$newThread->title, $oldThread->title]);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_ordered_by_date_of_creation_in_descending_order()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $oldThread = ThreadFactory::inCategory($category)
            ->createdAt(Carbon::now()->subDay())
            ->create();

        $response = $this->get(route(
            'category-threads.index',
            [$category, 'new_threads' => true]
        ));

        $response->assertSeeInOrder([$thread->title, $oldThread->title]);
    }

    /** @test */
    public function user_can_view_the_threads_with_the_most_recent_replies_in_descending_order()
    {
        $user = create(User::class);
        $recentlyActiveThread = ThreadFactory::create();
        ReplyFactory::toThread($recentlyActiveThread)->create();
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = ThreadFactory::create();
        ReplyFactory::toThread($recentlyActiveThread)->create();
        Carbon::setTestNow();

        $response = $this->get(route('threads.index', ['new_posts' => true]));

        $response->assertSeeInOrder([$recentlyActiveThread->title, $inactiveThread->title]);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_with_the_most_recent_replies_in_descending_order()
    {
        $category = create(Category::class);
        $recentlyActiveThread = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($recentlyActiveThread)->create();
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($inactiveThread)->create();
        Carbon::setTestNow();

        $response = $this->get(route(
            'category-threads.index',
            [$category, 'new_posts' => true]
        ));

        $response->assertSeeInOrder([$recentlyActiveThread->title, $inactiveThread->title]);
    }

    /** @test */
    public function a_user_can_view_the_threads_that_has_replied_to()
    {
        $threadWithoutParticipation = create(Thread::class);
        ReplyFactory::toThread($threadWithoutParticipation)->create();
        $threadWithoutReplies = create(Thread::class);
        $orestis = create(User::class);
        $threadWithParticipation = create(Thread::class);
        ReplyFactory::by($orestis)
            ->toThread($threadWithParticipation)
            ->create();

        $response = $this->get(route(
            'threads.index',
            ['contributed' => $orestis->name]
        ));

        $response->assertSee($threadWithParticipation->title);
        $response->assertDontSee($threadWithoutParticipation->title);
    }

    /** @test */
    public function a_user_can_view_the_threads_of_a_category_that_has_replied_to()
    {
        $category = create(Category::class);
        $threadWithoutParticipation = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($threadWithoutParticipation)->create();
        $threadWithoutReplies = create(Thread::class);
        $orestis = create(User::class);
        $threadWithParticipation = ThreadFactory::inCategory($category)->create();
        ReplyFactory::by($orestis)
            ->toThread($threadWithParticipation)
            ->create();

        $response = $this->get(route(
            'category-threads.index',
            [$category, 'contributed' => $orestis->name]
        ));

        $response->assertSee($threadWithParticipation->title);
        $response->assertDontSee($threadWithoutParticipation->title);
    }

    /** @test */
    public function user_can_view_trending_threads_in_descending_order()
    {
        $trendingThread = create(Thread::class, ['views' => 50]);
        ReplyFactory::toThread($trendingThread)->createMany(5);
        $lessTrendingThread = create(Thread::class, ['views' => 100]);
        ReplyFactory::toThread($lessTrendingThread)->create();

        $response = $this->get(route('threads.index', ['trending' => true]));

        $response->assertSeeInOrder([$trendingThread->title, $lessTrendingThread->title]);
    }

    /** @test */
    public function user_can_view_the_trending_threads_of_a_category_in_descending_order()
    {
        $category = create(Category::class);
        $trendingThread = ThreadFactory::inCategory($category)->create(['views' => 50]);
        ReplyFactory::toThread($trendingThread)->createMany(5);
        $lessTrendingThread = ThreadFactory::inCategory($category)->create(['views' => 100]);
        ReplyFactory::toThread($lessTrendingThread)->create();

        $response = $this->get(route('threads.index', [$category, 'trending' => true]));

        $response->assertSeeInOrder([$trendingThread->title, $lessTrendingThread->title]);
    }

    /** @test */
    public function user_can_view_the_unanswered_threads()
    {
        $threadWithoutReplies = create(Thread::class);
        $threadWithReplies = create(Thread::class);
        ReplyFactory::toThread($threadWithReplies)->createMany(5);

        $response = $this->get(route('threads.index', ['unanswered' => true]));

        $response->assertSee($threadWithoutReplies->title)
            ->assertDontSee($threadWithReplies->title);
    }

    /** @test */
    public function user_can_view_the_unanswered_threads_of_a_category()
    {
        $category = create(Category::class);
        $threadWithoutReplies = ThreadFactory::inCategory($category)->create();
        $threadWithReplies = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($threadWithReplies)->createMany(5);

        $response = $this->get(route('category-threads.index', [$category, 'unanswered' => true]));

        $response->assertSee($threadWithoutReplies->title)
            ->assertDontSee($threadWithReplies->title);
    }

    /** @test */
    public function a_user_can_view_the_threads_tha_has_subscribred_to()
    {
        $threadThatHasntSubscribedTo = create(Thread::class);
        $orestis = $this->signIn();
        $subscribedThread = create(Thread::class);
        $subscribedThread->subscribe($orestis->id);

        $response = $this->get(route('threads.index') . "?watched=1");

        $response->assertSee($subscribedThread->title)
            ->assertDontSee($threadThatHasntSubscribedTo->title);
    }

    /** @test */
    public function a_user_can_view_the_threads_of_a_category_tha_has_subscribred_to()
    {
        $category = create(Category::class);
        $threadThatHasntSubscribedTo = ThreadFactory::inCategory($category)->create();
        $orestis = $this->signIn();
        $subscribedThread = ThreadFactory::inCategory($category)->create();
        $subscribedThread->subscribe($orestis->id);

        $response = $this->get(route('category-threads.index', $category) . "?watched=1");

        $response->assertSee($subscribedThread->title)
            ->assertDontSee($threadThatHasntSubscribedTo->title);
    }

    /** @test */
    public function user_can_view_the_threads_that_have_been_last_updated_X_days_ago()
    {
        $todaysThread = create(Thread::class);
        $oldThread = ThreadFactory::updatedAt(Carbon::now()->subMonth())
            ->create();
        $daysAgo = 3;

        $response = $this->get(
            route('threads.index') . "?last_updated=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_that_have_been_last_updated_X_days_ago()
    {
        $category = create(Category::class);
        $todaysThread = ThreadFactory::inCategory($category)->create();
        $oldThread = ThreadFactory::inCategory($category)
            ->updatedAt(Carbon::now()->subMonth())
            ->create();
        $daysAgo = 3;

        $response = $this->get(
            route('category-threads.index', $category) . "?last_updated=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function user_can_view_the_threads_that_have_been_last_created_X_days_ago()
    {
        $todaysThread = create(Thread::class);
        $oldThread = ThreadFactory::createdAt(Carbon::now()->subMonth())
            ->create();
        $daysAgo = 3;

        $response = $this->get(
            route('threads.index') . "?last_created=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_that_have_been_last_created_X_days_ago()
    {
        $category = create(Category::class);
        $todaysThread = ThreadFactory::inCategory($category)->create();
        $oldThread = ThreadFactory::inCategory($category)
            ->createdAt(Carbon::now()->subMonth())
            ->create();
        $daysAgo = 3;

        $response = $this->get(
            route('category-threads.index', $category) . "?last_created=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function users_can_view_pinned_threads()
    {
        createMany(Thread::class, 50);
        $oneYearAgo = Carbon::now()->subYear();
        $pinnedThread = ThreadFactory::createdAt($oneYearAgo)
            ->updatedAt($oneYearAgo)
            ->create();
        $pinnedThread->pin();

        $response = $this->get(route('threads.index'));

        $response->assertSee($pinnedThread->title);
    }

    /** @test */
    public function users_can_view_pinned_threads_of_a_category_if_exist()
    {
        $category = create(Category::class);
        ThreadFactory::inCategory($category)->createMany(50);
        $oneYearAgo = Carbon::now()->subYear();
        $pinnedThread = ThreadFactory::createdAt($oneYearAgo)
            ->updatedAt($oneYearAgo)
            ->inCategory($category)
            ->create();
        $pinnedThread->pin();

        $response = $this->get(route('category-threads.index', $category));

        $response->assertSee($pinnedThread->title);
    }
}
