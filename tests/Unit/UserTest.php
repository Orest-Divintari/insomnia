<?php

namespace Tests\Unit;

use App\Activity;
use App\Conversation;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /** @test */
    public function a_user_can_determine_the_path_to_his_avatar()
    {
        $avatar = '/avatars/users/user_logo.png';
        $user = create(User::class, ['avatar_path' => $avatar]);

        $this->assertEquals(asset($avatar), $user->avatar_path);
    }

    /** @test */
    public function user_has_a_shorter_version_of_his_name()
    {
        $user = create(User::class, ['name' => $this->faker->sentence()]);

        $this->assertEquals(
            Str::limit($user, config('contants.user.name_limit'), ''),
            $user->shorName
        );
    }

    /** @test */
    public function a_user_may_like_a_post()
    {
        $user = create(User::class);
        $thread = create(Thread::class);
        $reply = ReplyFactory::toThread($thread)->create();

        $reply->likedBy($user);

        $this->assertCount(1, $user->likes);
    }

    /** @test */
    public function a_user_has_subscriptions()
    {
        $user = create(User::class);
        $thread = create(Thread::class);

        $thread->subscribe($user->id);

        $this->assertCount(1, $user->subscriptions);
    }

    /** @test */
    public function a_user_can_fetch_the_subscription_for_a_specific_thread()
    {
        $user = create(User::class);
        $thread = create(Thread::class);

        $thread->subscribe($user->id);

        $this->assertEquals(
            $user->id,
            $user->subscription($thread->id)->user_id
        );
        $this->assertEquals(
            $thread->id,
            $user->subscription($thread->id)->thread_id
        );

    }

    /** @test */
    public function a_user_has_replies()
    {
        $user = create(User::class);
        ReplyFactory::by($user)->create();

        $this->assertCount(1, $user->replies);
    }

    /** @test */
    public function a_user_has_messages_count_which_is_the_number_of_profile_posts_on_his_profile()
    {
        $user = create(User::class);
        ProfilePostFactory::toProfile($user)->create();

        $user = User::withMessagesCount()
            ->whereId($user->id)
            ->first();

        $this->assertEquals(1, $user->messages_count);
    }

    /** @test */
    public function it_knows_the_number_of_likes_of_posts()
    {
        $user = create(User::class);
        $thread = create(Thread::class);
        $reply = ReplyFactory::by($user)->create();
        $anotherUser = create(User::class);
        $reply->likedBy($anotherUser);

        $user = User::withLikesCount()->whereId($user->id)->first();

        $this->assertEquals(1, $user->likes_count);
    }

    /** @test */
    public function a_user_has_profile_posts()
    {
        $user = create(User::class);

        ProfilePostFactory::by($user)
            ->toProfile($user)
            ->create();

        $this->assertCount(1, $user->profilePosts);
    }

    /** @test */
    public function a_user_can_post_to_another_user_profile()
    {
        $user = $this->signIn();
        $profileOwner = create(User::class);
        $post = ['body' => 'some body'];

        $newPost = $user->postToProfile($post['body'], $profileOwner);

        $this->assertEquals($newPost['body'], $post['body']);
        $this->assertDatabaseHas('profile_posts', [
            'body' => $post['body'],
            'profile_owner_id' => $profileOwner->id,
            'user_id' => auth()->id(),
        ]);
    }

    /** @test */
    public function a_user_knows_the_message_count_which_is_the_number_of_posts_on_his_profile()
    {
        $profileOwner = create(User::class);
        ProfilePostFactory::toProfile($profileOwner)->create();

        $profileOwner = User::withMessagesCount()
            ->whereId($profileOwner->id)
            ->first();

        $this->assertEquals(1, $profileOwner->messages_count);
    }

    /** @test */
    public function a_user_knows_the_like_score_which_is_how_many_times_his_replies_are_liked()
    {
        $user = create(User::class);
        $reply = ReplyFactory::by($user)->create();
        $comment = CommentFactory::by($user)->create();
        $liker = $this->signIn();

        $comment->likedBy($liker);
        $reply->likedBy($liker);
        $user = User::withLikesCount()
            ->whereId($user->id)
            ->first();

        $this->assertEquals(2, $user->likes_count);
    }

    /** @test */
    public function a_user_can_participate_to_conversations()
    {
        $user = $this->signIn();

        $conversation = create(Conversation::class);

        $this->assertCount(1, $user->conversations);
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function a_user_has_conversations()
    {
        $john = $this->signIn();
        $orestis = create(User::class);
        $conversationA = ConversationFactory::by($john)
            ->withParticipants([$orestis->name])
            ->create();
        $this->signIn($orestis);

        $conversationB = ConversationFactory::by($orestis)
            ->withParticipants([$john->name])
            ->create();

        $this->assertCount(2, $orestis->conversations);
    }

    /** @test */
    public function get_the_unread_conversations()
    {
        $conversationStarter = $this->signIn();
        $conversation = create(
            Conversation::class,
            ['user_id' => $conversationStarter->id]
        );

        $this->assertCount(1, $conversationStarter->unreadConversations);
    }

    /** @test */
    public function a_user_knows_if_is_admin()
    {
        $notAdminUser = create(User::class);

        $adminUser = create(User::class, ['email' => 'uric@example.com']);

        $this->assertFalse($notAdminUser->isAdmin());
        $this->assertTrue($adminUser->isAdmin());
    }

    /** @test */
    public function when_a_user_is_deleted_the_associated_activities_are_deleted()
    {
        $user = $this->signIn();
        $userId = $user->id;
        $thread = ThreadFactory::by($user)->create();

        $user->delete();

        $this->assertCount(0, Activity::where('user_id', $userId)->get());
    }

    /** @test */
    public function when_a_user_is_deleted_the_associated_threads_are_deleted()
    {
        $user = create(User::class);
        $thread = create(Thread::class, ['user_id' => $user->id]);
        $this->assertEquals(1, Thread::count());

        $user->delete();

        $this->assertEquals(0, Thread::count());
    }

    /** @test */
    public function a_user_knows_which_is_the_last_post_activity()
    {
        $user = $this->signIn();
        Carbon::setTestNow(Carbon::now()->subDay());
        ReplyFactory::by($user)->create();
        $profilePost = ProfilePostFactory::by($user)->create();
        Carbon::setTestNow();
        $comment = CommentFactory::by($user)
            ->toProfilePost($profilePost)
            ->create();

        $lastPostActivity = $user->lastPostActivity();

        $this->assertEquals($comment->id, $lastPostActivity->subject->id);
    }

    /** @test */
    public function it_knows_if_is_followed_by_profile_visitor()
    {
        $profileOwner = create(User::class);
        $visitor = create(User::class);
        $visitor->follow($profileOwner);
        $this->signIn($visitor);

        $user = User::withFollowedByVisitor()->whereName($profileOwner->name)->first();

        $this->assertTrue($user->followed_by_visitor);
    }

    /** @test */
    public function it_knows_the_profile_info()
    {
        $profileOwner = create(User::class);

        $user = User::select()->withProfileInfo()->whereName($profileOwner->name)->first()->toArray();

        $this->assertArrayHasKey('messages_count', $user);
        $this->assertArrayHasKey('likes_count', $user);
        $this->assertArrayHasKey('followed_by_visitor', $user);
    }

    /** @test */
    public function can_mark_notifications_as_viewed()
    {
        $orestis = $this->signIn();
        $thread = create(Thread::class);
        $thread->subscribe($orestis->id);
        $john = create(User::class);
        $thread->addReply(['body' => $this->faker->sentence], $john);
        Carbon::setTestNow(Carbon::now()->addDay());
        $this->assertFalse($orestis->notificationsViewed());

        $orestis->viewNotifications();

        $this->assertTrue($orestis->notificationsViewed());
    }
}