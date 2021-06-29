<?php

namespace Tests\Feature;

use App\Events\Conversation\NewMessageWasAddedToConversation;
use App\Notifications\CommentHasNewLike;
use App\Notifications\MessageHasNewLike;
use App\Notifications\ParticipatedProfilePostHasNewComment;
use App\Notifications\PostOnYourProfileHasNewComment;
use App\Notifications\ProfileHasNewPost;
use App\Notifications\ProfilePostHasNewLike;
use App\Notifications\ReplyHasNewLike;
use App\Notifications\ThreadHasNewReply;
use App\Notifications\YouHaveANewFollower;
use App\Notifications\YourPostOnYourProfileHasNewComment;
use App\Notifications\YourProfilePostHasNewComment;
use App\Thread;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class IgnoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_be_ignored_by_another_user()
    {
        $john = $this->signIn();
        $doe = create(User::class);

        $this->post(route('ajax.user-ignorations.store', $doe));

        $this->assertTrue($doe->isIgnored($john));
    }

    /** @test */
    public function a_user_cannot_be_ignored_twice_by_the_same_user()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.user-ignorations.store', $doe));

        $this->assertCount(1, $doe->ignorations);
    }

    /** @test */
    public function a_user_can_be_uningored_by_another_user()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $doe->markAsIgnored($john);

        $this->delete(route('ajax.user-ignorations.destroy', $doe));

        $this->assertFalse($doe->isIgnored($john));
    }

    /** @test */
    public function a_user_can_mark_a_thread_as_ignored()
    {
        $john = $this->signIn();
        $thread = create(Thread::class);

        $this->post(route('ajax.thread-ignorations.store', $thread));

        $this->assertTrue($thread->isIgnored($john));
    }

    /** @test */
    public function a_user_can_mark_a_thread_as_ignored_only_once()
    {
        $john = $this->signIn();
        $thread = create(Thread::class);
        $thread->markAsIgnored($john);

        $this->post(route('ajax.thread-ignorations.store', $thread));

        $this->assertCount(1, $thread->ignorations);
    }

    /** @test */
    public function a_user_can_mark_a_thread_as_unignored()
    {
        $this->withoutExceptionHandling();
        $john = $this->signIn();
        $thread = create(Thread::class);
        $thread->markAsIgnored($john);

        $this->delete(route('ajax.thread-ignorations.destroy', $thread));

        $this->assertFalse($thread->isIgnored($john));
    }

    /** @test */
    public function it_returns_the_followers_except_the_ignored_users()
    {
        $john = create(User::class);
        $doe = $this->signIn();
        $doe->follow($john);
        $bob = $this->signIn();
        $bob->follow($john);
        $this->signIn($john);
        $doe->markAsIgnored($john);

        $response = $this->get(route('ajax.followed-by.index', $john));

        $followers = $response->json()['data'];
        $this->assertCount(1, $followers);
    }

    /** @test */
    public function it_returns_all_thread_replies_including_thread_replies_created_by_ignored_users()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $bob = create(User::class);
        $thread = ThreadFactory::by($john)->create();
        $replyByDoe = ReplyFactory::by($doe)->toThread($thread)->create();
        $doe->markAsIgnored($john);
        ReplyFactory::by($bob)->toThread($thread)->create();

        $response = $this->get(route('threads.show', $thread));

        $replies = collect($response['replies']->items());
        $ignoredReply = $replies->firstWhere('id', $replyByDoe->id);
        $this->assertTrue($ignoredReply['ignored_by_visitor']);
        $unignoredReplies = $replies->filter(function ($reply) use ($ignoredReply) {
            return $reply->isNot($ignoredReply);
        });
        $this->assertTrue(
            $unignoredReplies->every(function ($reply) {
                return !$reply['ignored_by_visitor'];
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
        $doe->markAsIgnored($john);
        $this->signIn($john);

        $response = $this->get(route('threads.index'));

        $threads = collect($response['normalThreads']->items());
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
        $ignoredThread->markAsIgnored($john);
        $this->signIn($john);

        $response = $this->get(route('threads.index'));

        $threads = collect($response['normalThreads']->items());
        $this->assertCount(1, $threads);
        $this->assertFalse($threads->search(function ($thread) use ($ignoredThread) {
            return $thread->id == $ignoredThread->id;
        }));
    }

    /** @test */
    public function it_returns_the_profile_post_comments_created_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $profilePost = ProfilePostFactory::toProfile($john)->create();
        $commentByDoe = CommentFactory::by($doe)
            ->toProfilePost($profilePost)
            ->create();
        $commentByBob = CommentFactory::by($bob)
            ->toProfilePost($profilePost)
            ->create();
        $doe->markAsIgnored($john);
        $this->signIn($john);

        $response = $this->get(route('profiles.show', $john));

        $comments = collect($response['profilePosts']->items()[0]['paginatedComments']->items());
        $this->assertCount(1, $comments);
        $this->assertFalse($comments->search(function ($comment) use ($commentByDoe) {
            return $comment->is($commentByDoe);
        }));
    }

    /** @test */
    public function it_returns_profile_posts_created_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $profilePostByDoe = ProfilePostFactory::by($doe)
            ->toProfile($john)
            ->create();
        $profilePostByBob = ProfilePostFactory::by($bob)
            ->toProfile($john)
            ->create();
        $doe->markAsIgnored($john);
        $this->signIn($john);

        $response = $this->get(route('profiles.show', $john));

        $profilePosts = collect($response['profilePosts']->items());
        $this->assertCount(1, $profilePosts);
        $this->assertFalse($profilePosts->search(function ($profilePost) use ($profilePostByDoe) {
            return $profilePost->id == $profilePostByDoe->id;
        }));
    }

    /** @test */
    public function it_returns_conversations_created_only_by_users_that_are_not_ignored()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $this->signIn($doe);
        $conversationByDoe = ConversationFactory::by($doe)->withParticipants([$john->name])->create();
        $this->signIn($bob);
        $conversationByBob = ConversationFactory::by($bob)->withParticipants([$john->name])->create();
        $this->signIn($john);
        $doe->markAsIgnored($john);

        $response = $this->get(route('conversations.index'));

        $conversations = collect($response['conversations']->items());
        $this->assertCount(1, $conversations);
        $this->assertFalse($conversations->search(function ($conversation) use ($conversationByDoe) {
            return $conversation->id == $conversationByDoe->id;
        }));
    }

    /** @test */
    public function it_returns_conversations_only_by_users_that_are_not_ignored_with_ajax_request()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $bob = create(User::class);
        $this->signIn($doe);
        $conversationByDoe = ConversationFactory::by($doe)->withParticipants([$john->name])->create();
        $this->signIn($bob);
        $conversationByBob = ConversationFactory::by($bob)->withParticipants([$john->name])->create();
        $this->signIn($john);
        $doe->markAsIgnored($john);

        $response = $this->get(route('ajax.conversations.index'));
        $conversations = collect($response->json());
        $this->assertCount(1, $conversations);
        $this->assertFalse($conversations->search(function ($conversation) use ($conversationByDoe) {
            return $conversation['id'] == $conversationByDoe->id;
        }));
    }

    /** @test */
    public function it_returns_all_likes_even_by_users_that_are_ignored()
    {
        $doe = create(User::class);
        $john = create(User::class);
        $reply = ReplyFactory::by($john)->create();

        $this->signIn($doe);
        $reply->likedBy($doe);
        $this->signIn($john);

        $response = $this->get(route('threads.show', $reply->repliable));

        $reply = $response['replies'][1];
        $this->assertEquals(1, $reply['likes_count']);
    }

    /** @test */
    public function it_returns_all_messages_of_a_conversation_even_by_users_that_are_ignored()
    {
        $john = $this->signIn();
        $doe = create(User::class);
        $bob = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$doe->name, $bob->name])
            ->create();

        $messageByDoe = $conversation->addMessage(['body' => $this->faker()->sentence()], $doe);
        $conversation->addMessage(['body' => $this->faker()->sentence()], $bob);
        $doe->markAsIgnored($john);

        $response = $this->get(route('conversations.show', $conversation));

        $messages = collect($response['messages']->items());
        $this->assertCount(3, $messages);
        $ignoredMessage = $messages->firstWhere('id', $messageByDoe->id);
        $this->assertTrue($ignoredMessage['ignored_by_visitor']);

        $unignoredMessages = $messages->filter(function ($message) use ($ignoredMessage) {
            return $message->isNot($ignoredMessage);
        });
        $unignoredMessages->every(function ($message) {
            return !$message['ignored_by_visitor'];
        });
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_an_ignored_user_replies_to_a_thread()
    {
        Notification::fake();
        $john = $this->signIn();
        $thread = ThreadFactory::by($john)->create();
        $doe = create(User::class);
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.replies.store', $thread), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, ThreadHasNewReply::class);
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_their_thread_replies_are_liked_by_ignored_users()
    {
        $this->withoutExceptionHandling();
        Notification::fake();
        $john = $this->signIn();
        $reply = ReplyFactory::by($john)->create();
        $doe = create(User::class);
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.reply-likes.store', $reply));

        Notification::assertNotSentTo($john, ReplyHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_their_profile_post_comments_are_liked_by_ignored_users()
    {
        Notification::fake();
        $john = $this->signIn();
        $comment = CommentFactory::by($john)->create();
        $doe = create(User::class);
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.reply-likes.store', $comment));

        Notification::assertNotSentTo($john, CommentHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_email_notifications_when_an_ignored_user_sends_a_message()
    {
        Notification::fake();
        $john = $this->signIn();
        $doe = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$doe->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.messages.store', $conversation), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, NewMessageWasAddedToConversation::class);
    }

    /** @test */
    public function users_will_not_receive_a_notification_when_their_conversation_messages_are_liked_by_ignored_users()
    {
        Notification::fake();
        $john = $this->signIn();
        $doe = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$doe->name])
            ->create();
        $message = $conversation->messages()->first();
        $this->signIn($doe);
        $doe->markAsIgnored($john);

        $this->post(route('ajax.reply-likes.store', $message));

        Notification::assertNotSentTo($john, MessageHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_their_profile_is_created_a_post_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.profile-posts.store', $john), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, ProfileHasNewPost::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_their_own_post_on_their_profile_a_new_comment_is_added_by_an_ingored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $profilePost = ProfilePostFactory::by($john)->toProfile($john)->create();
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, YourPostOnYourProfileHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_their_profile_on_a_profile_post_by_another_user_a_new_comment_is_added_by_an_ingored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $doe = $this->signIn();
        $profilePost = ProfilePostFactory::by($doe)->toProfile($john)->create();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, PostOnYourProfileHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_their_profile_post_on_another_user_profile_a_new_comment_is_added_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $bob = create(User::class);
        $doe = $this->signIn();
        $profilePost = ProfilePostFactory::by($john)->toProfile($bob)->create();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, YourProfilePostHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_on_a_profile_post_that_the_user_has_commented_a_new_comment_is_added_by_an_ingored_user_adds_a_commentwhen_their_profile_post_on_another_user_profile_a_new_comment_is_added_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $bob = create(User::class);
        $profilePost = ProfilePostFactory::by($bob)->toProfile($bob)->create();
        CommentFactory::by($john)->toProfilePost($profilePost);
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.comments.store', $profilePost), ['body' => $this->faker()->sentence()]);

        Notification::assertNotSentTo($john, ParticipatedProfilePostHasNewComment::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_their_profile_post_is_liked_by_an_ignored_user()
    {
        Notification::fake();
        $john = create(User::class);
        $bob = create(User::class);
        $profilePost = ProfilePostFactory::by($john)->toProfile($bob)->create();
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.profile-post-likes.store', $profilePost));

        Notification::assertNotSentTo($john, ProfilePostHasNewLike::class);
    }

    /** @test */
    public function users_will_not_receive_notifications_when_an_ignored_user_started_following_them()
    {
        Notification::fake();
        $john = create(User::class);
        $doe = $this->signIn();
        $doe->markAsIgnored($john);

        $this->post(route('ajax.follow.store', $john));

        Notification::assertNotSentTo($john, YouHaveANewFollower::class);
    }

}