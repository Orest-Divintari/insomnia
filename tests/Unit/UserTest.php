<?php

namespace Tests\Unit;

use App\Conversation;
use App\Like;
use App\ProfilePost;
use App\Read;
use App\Reply;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ReplyFactory;
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
    public function an_authenticated_user_can_mark_a_thread_as_read()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $this->assertTrue($thread->hasBeenUpdated);

        $user->read($thread);
        $this->assertFalse($thread->hasBeenUpdated);
    }

    /** @test */
    public function a_user_may_like_a_post()
    {
        $user = create(User::class);
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        Like::create([
            'reply_id' => $reply->id,
            'user_id' => $user->id,
        ]);

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

        $thread = create(Thread::class);

        $thread->addReply(raw(Reply::class, [
            'user_id' => $user->id,
        ]));

        $this->assertCount(1, $user->replies);
    }

    /** @test */
    public function a_user_has_messages_count_which_is_the_number_of_profile_posts_on_his_profile()
    {
        $user = create(User::class);

        create(ProfilePost::class, ['profile_owner_id' => $user->id]);

        $this->assertEquals(1, $user->fresh()->message_count);

    }

    /** @test */
    public function a_user_has_a_likes_score()
    {
        $user = create(User::class);

        $thread = create(Thread::class);

        $reply = $thread->addReply(raw(Reply::class, [
            'user_id' => $user->id,
        ]));
        $anotherUser = create(User::class);

        $reply->likedBy($anotherUser);

        $this->assertEquals(1, $user->fresh()->like_score);

    }

    /** @test */
    public function a_user_has_profile_posts()
    {
        $user = create(User::class);

        create(ProfilePost::class, [
            'user_id' => $user->id,
            'profile_owner_id' => $user->id,
        ]);

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

        create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );

        $profileOwner = User::withMessageCount()
            ->whereId($profileOwner->id)
            ->first();

        $this->assertEquals(1, $profileOwner->message_count);
    }

    /** @test */
    public function a_user_knows_the_like_score_which_is_how_many_times_his_replies_are_liked()
    {
        $user = create(User::class);

        $reply = ReplyFactory::create(['user_id' => $user->id]);
        $comment = CommentFactory::create(['user_id' => $user->id]);

        $liker = $this->signIn();

        $comment->likedBy($liker);
        $reply->likedBy($liker);

        $user = User::withLikeScore()
            ->whereId($user->id)
            ->first();

        $this->assertEquals(2, $user->like_score);
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
    public function a_user_can_mark_a_conversation_as_read()
    {
        $this->withoutExceptionHandling();
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $conversationStarter->read($conversation);

        $this->assertFalse($conversation->hasBeenUpdated);
    }

    /** @test */
    public function a_user_can_mark_a_conversation_as_unread()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $conversationStarter->read($conversation);

        $this->assertFalse($conversation->hasBeenUpdated);

        $conversationStarter->unread($conversation);

        $this->assertTrue($conversation->hasBeenUpdated);
    }

    /** @test */
    public function get_the_unread_conversations()
    {
        $conversationStarter = $this->signIn();
        $conversation = create(
            Conversation::class,
            ['user_id' => $conversationStarter->id]
        );
        $this->assertCount(1, $conversationStarter->fresh()->unreadConversations);

        $conversationStarter->read($conversation);
        $this->assertCount(0, $conversationStarter->fresh()->unreadConversations);

        $participant = create(User::class);
        $this->assertCount(0, $participant->unreadConversations);

        $conversation->addParticipants([$participant->name]);
        $this->assertCount(1, $participant->fresh()->unreadConversations);

        $participant->read($conversation);
        $this->assertCount(0, $participant->fresh()->unreadConversations);

        Carbon::setTestNow(Carbon::now()->addDay());
        $conversation->addMessage('random message', $participant);
        $this->assertCount(1, $conversationStarter->fresh()->unreadConversations);
        $this->assertCount(1, $participant->fresh()->unreadConversations);
    }

    /** @test */
    public function a_user_knows_if_is_admin()
    {
        $notAdminUser = create(User::class);

        $adminUser = create(User::class, ['email' => 'uric@example.com']);

        $this->assertFalse($notAdminUser->isAdmin());
        $this->assertTrue($adminUser->isAdmin());
    }
}