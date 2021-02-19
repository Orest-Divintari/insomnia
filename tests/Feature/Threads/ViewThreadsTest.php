<?php

namespace Tests\Feature\Threads;

use App\Category;
use App\Reply;
use App\Thread;
use App\User;
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

        $response = $this->get(route('threads.index') . "?postedBy={$uric->name}");

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

        $response = $this->get(route('category-threads.index', $category) . "?postedBy={$uric->name}");

        $response->assertSee($threadByUric->title)
            ->assertDontSee($threadByJohn->title);
    }

    /** @test */
    public function user_can_view_the_threads_ordered_by_date_of_creation_in_descending_order()
    {
        $newThread = create(Thread::class);
        $oldThread = ThreadFactory::createdAt(Carbon::now()->subDay())->create();

        $response = $this->getJson(route('threads.index') . '?newThreads=1');

        $this->assertEquals($newThread->id, $response['data'][0]['id']);
        $this->assertEquals($oldThread->id, $response['data'][1]['id']);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_ordered_by_date_of_creation_in_descending_order()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $oldThread = ThreadFactory::inCategory($category)
            ->createdAt(Carbon::now()->subDay())
            ->create();

        $response = $this->getJson(route('category-threads.index', $category) . '?newThreads=1');

        $this->assertEquals($thread->id, $response['data'][0]['id']);
        $this->assertEquals($oldThread->id, $response['data'][1]['id']);
    }

    /** @test */
    public function user_can_fetch_the_threads_with_the_most_recent_replies()
    {
        $user = create(User::class);
        $recentlyActiveThread = ThreadFactory::create();
        ReplyFactory::toThread($recentlyActiveThread)->create();
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = ThreadFactory::create();
        ReplyFactory::toThread($recentlyActiveThread)->create();
        Carbon::setTestNow();

        $threads = $this->getJson(
            route('threads.index') . "?newPosts=1"
        )->json()['data'];

        $this->assertCount(2, $threads);
        $this->assertEquals($recentlyActiveThread->id, $threads[0]['id']);
        $this->assertEquals($inactiveThread->id, $threads[1]['id']);
    }

    /** @test */
    public function user_can_fetch_the_threads_of_a_category_with_the_most_recent_replies()
    {
        $category = create(Category::class);
        $recentlyActiveThread = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($recentlyActiveThread)->create();
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($inactiveThread)->create();
        Carbon::setTestNow();

        $threads = $this->getJson(
            route('category-threads.index', $category) . "?newPosts=1"
        )->json()['data'];

        $this->assertCount(2, $threads);
        $this->assertEquals($recentlyActiveThread->id, $threads[0]['id']);
        $this->assertEquals($inactiveThread->id, $threads[1]['id']);
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

        $response = $this->getJson(
            route('threads.index') . "?contributed=" . $orestis->name
        )->json();

        $this->assertEquals(
            $orestis->id,
            $response['data'][0]['recent_reply']['poster']['id']
        );
    }

    /** @test */
    public function a_user_can_view_the_threads_of_a_category_that_has_replied_to()
    {
        $category = create(Category::class);
        $threadWithoutParticipation = ThreadFactory::inCategory($category)->create();
        ReplyFactory::toThread($threadWithoutParticipation)->create();
        $threadWithoutReplies = ThreadFactory::inCategory($category)->create();
        $orestis = create(User::class);
        ReplyFactory::by($orestis)->create();

        $response = $this->getJson(
            route('threads.index') . "?contributed=" . $orestis->name
        )->json();

        $this->assertEquals(
            $orestis->id,
            $response['data'][0]['recent_reply']['poster']['id']
        );
    }

    /** @test */
    public function user_can_view_trending_threads()
    {
        $trendingThread = create(Thread::class, ['views' => 50]);
        ReplyFactory::toThread($trendingThread)->createMany(5);

        $lessTrendingThread = create(Thread::class, ['views' => 100]);
        ReplyFactory::toThread($lessTrendingThread)->create();

        $response = $this->getJson(
            route('threads.index') . "?trending=1"
        );

        $this->assertEquals(
            $trendingThread->id,
            $response['data'][0]['id']
        );
        $this->assertEquals(
            $lessTrendingThread->id,
            $response['data'][1]['id']
        );
    }

    /** @test */
    public function user_can_view_the_trending_threads_of_a_category()
    {
        $category = create(Category::class);
        $trendingThread = ThreadFactory::inCategory($category)->create(['views' => 50]);
        ReplyFactory::toThread($trendingThread)->createMany(5);
        $lessTrendingThread = ThreadFactory::inCategory($category)->create(['views' => 100]);
        ReplyFactory::toThread($lessTrendingThread)->create();

        $response = $this->getJson(
            route('category-threads.index', $category) . "?trending=1"
        );

        $this->assertEquals(
            $trendingThread->id,
            $response['data'][0]['id']
        );
        $this->assertEquals(
            $lessTrendingThread->id,
            $response['data'][1]['id']
        );
    }

    /** @test */
    public function user_can_view_the_unanswered_threads()
    {
        $threadWithoutReplies = create(Thread::class);
        $threadWithReplies = create(Thread::class);
        ReplyFactory::toThread($threadWithReplies)->createMany(5);

        $response = $this->get(route('threads.index') . "?unanswered=1");

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

        $response = $this->get(route('category-threads.index', $category) . "?unanswered=1");

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
            route('threads.index') . "?lastUpdated=" . $daysAgo
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
            route('category-threads.index', $category) . "?lastUpdated=" . $daysAgo
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
            route('threads.index') . "?lastCreated=" . $daysAgo
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
            route('category-threads.index', $category) . "?lastCreated=" . $daysAgo
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

    /** @test */
    public function view_the_last_pages_of_replies_for_a_thread_of_a_given_category()
    {
        $thread = create(Thread::class);
        $category = $thread->category;
        $pages = 10;
        $threadBody = 1;
        $thread->increment('replies_count', Thread::REPLIES_PER_PAGE * $pages - $threadBody);

        $threads = $this->getJson(route('category-threads.index', $category))->json()['data'];
        $this->assertEquals(
            $thread->linkToPage(8),
            $threads[0]['last_pages'][8]
        );
        $this->assertEquals(
            $thread->linkToPage(9),
            $threads[0]['last_pages'][9]
        );
        $this->assertEquals(
            $thread->linkToPage(10),
            $threads[0]['last_pages'][10]
        );
    }

    /** @test */
    public function view_the_last_pages_of_replies_for_a_thread()
    {
        $thread = create(Thread::class);
        $pages = 10;
        $threadBody = 1;
        $thread->increment('replies_count', Thread::REPLIES_PER_PAGE * $pages - $threadBody);

        $threads = $this->getJson(route('threads.index'))->json()['data'];

        $this->assertEquals(
            $thread->linkToPage(8),
            $threads[0]['last_pages'][8]
        );
        $this->assertEquals(
            $thread->linkToPage(9),
            $threads[0]['last_pages'][9]
        );
        $this->assertEquals(
            $thread->linkToPage(10),
            $threads[0]['last_pages'][10]
        );
    }
}