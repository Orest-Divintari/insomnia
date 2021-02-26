<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Tests\Feature\Search\SearchAllPostsTest;

class SearchAllPostsWithoutSearchQueryTest extends SearchAllPostsTest
{

    /** @test */
    public function get_all_posts_that_are_created_by_a_given_username()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $user = $this->signIn();
        $desiredThread = ThreadFactory::by($user)->create();
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();

        $results = $this->search([
            'posted_by' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount($this->totalNumberOfDesiredItems, $results);
        $this->assertContainsProfilePost($results, $desiredProfilePost);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsComment($results, $desiredComment);

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
    }

    /** @test */
    public function get_all_posts_given_multiple_usernames()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->toThread($threadByDoe)
            ->create();
        $profilePostByDoe = ProfilePostFactory::by($doe)->create();
        $commentByDoe = CommentFactory::by($doe)
            ->toProfilePost($profilePostByDoe)
            ->create();
        $profilePostByJohn = ProfilePostFactory::by($john)->create();
        $commentByJohn = CommentFactory::by($john)
            ->toProfilePost($profilePostByJohn)
            ->create();
        $usernames = $john->name . ',' . $doe->name;
        // the number of desired posts

        $numberOfDesiredItems = 8;
        $results = $this->search([
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertContainsComment($results, $commentByDoe);
        $this->assertContainsComment($results, $commentByJohn);
        $this->assertContainsProfilePost($results, $profilePostByDoe);
        $this->assertContainsProfilePost($results, $profilePostByJohn);
        $this->assertContainsThread($results, $threadByDoe);
        $this->assertContainsThread($results, $threadByJohn);
        $this->assertContainsThreadReply($results, $threadReplyByDoe);
        $this->assertContainsThreadReply($results, $threadReplyByJohn);

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $threadByJohn->delete();
        $threadByDoe->delete();
        $profilePostByDoe->delete();
        $profilePostByJohn->delete();
    }

    /** @test */
    public function get_all_posts_that_were_created_by_a_given_username_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $desiredThread = ThreadFactory::by($user)->create();
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();
        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);

        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        $anotherUndesiredThread = ThreadFactory::by($user)->create();
        $anotherUndesiredReply = ReplyFactory::by($user)
            ->toThread($anotherUndesiredThread)
            ->create();

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount($this->totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
        $anotherUndesiredThread->delete();
        $anotherUndesiredProfilePost->delete();
    }
}