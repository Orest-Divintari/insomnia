<?php

namespace Tests\Feature\Activities;

use App\Events\Activity\UserViewedPage;
use App\Http\Middleware\MustBeVerified;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Thread;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineUserActivitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_may_not_view_the_current_activities_of_online_users()
    {
        $response = $this->get(route('online-user-activities.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function the_activities_of_unverified_users_should_not_be_recorded()
    {
        $unverifiedUser = $this->signInUnverified();

        $response = $this->get(route('forum'));

        $this->assertDatabaseMissing('activities', [
            'description' => UserViewedPage::FORUM,
        ]);

        $this->signIn();

        $response = $this->get(route('online-user-activities.index'));

        $response->assertDontSee(UserViewedPage::FORUM);
    }

    /** @test */
    public function unverified_users_should_not_see_the_current_activities_of_online_users()
    {
        $unverifiedUser = $this->signInUnverified();

        $response = $this->get(route('online-user-activities.index'));

        $response->assertForbidden()
            ->assertSee(MustBeVerified::EXCEPTION_MESSAGE);
    }

    /** @test */
    public function members_may_view_the_latest_viewed_activity_of_guest_online_users()
    {
        $thread = create(Thread::class);
        $this->get(route('threads.show', $thread));
        $this->get(route('forum'));
        $this->assertCount(2, Activity::typeViewed()->get());
        $this->signIn();

        $response = $this->get(route('online-user-activities.index'));

        $response->assertSee('Guest');
        $response->assertSee(UserViewedPage::FORUM);
        $response->assertDontSee(UserViewedPage::THREAD);
    }

    /** @test */
    public function members_may_view_the_latest_view_activity_of_other_members()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->get(route('threads.show', $thread));
        $this->get(route('forum'));
        $this->assertCount(2, Activity::typeViewed()->get());

        $response = $this->get(route('online-user-activities.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::FORUM);
        $response->assertDontSee(UserViewedPage::THREAD);
    }

    /** @test */
    public function members_may_hide_their_current_activity()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $user->disallow('show_current_activity');
        $this->get(route('threads.show', $thread));
        $this->get(route('forum'));

        $response = $this->get(route('online-user-activities.index'));

        $response->assertDontSeeText(UserViewedPage::FORUM);
        $response->assertDontSeeText(UserViewedPage::THREAD);
    }

    /** @test */
    public function when_a_user_visits_a_thread_the_activity_is_displayed()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->get(route('threads.show', $thread));

        $response = $this->get(route('online-user-activities.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::THREAD);
        $response->assertSee($thread->title);
    }

    /** @test */
    public function when_a_user_visits_a_category_the_activity_is_displayed()
    {
        $category = create(Category::class);
        $user = $this->signIn();
        $this->get(route('categories.show', $category));

        $response = $this->get(route('online-user-activities.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::CATEGORY);
        $response->assertSee($category->title);
    }

    /** @test */
    public function when_a_user_visits_the_list_of_conversations_the_activity_is_displayed()
    {
        $user = $this->signIn();
        $this->get(route('conversations.index'));

        $response = $this->get(route('online-user-activities.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::CONVERSATION);
    }

    /** @test */
    public function when_a_user_opens_a_conversation_the_activity_is_displayed()
    {
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $this->get(route('conversations.show', $conversation));

        $response = $this->get(route('online-user-activities.index'));

        $response->assertSee(UserViewedPage::CONVERSATION);
    }

}