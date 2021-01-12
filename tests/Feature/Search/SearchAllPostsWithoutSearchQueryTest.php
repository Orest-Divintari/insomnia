<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\Thread;
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
            'postedBy' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->makeAssertions(
            $results,
            $desiredThread,
            $desiredReply,
            $desiredProfilePost,
            $desiredComment
        );

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
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
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->makeAssertions(
            $results,
            $desiredThread,
            $desiredReply,
            $desiredProfilePost,
            $desiredComment
        );

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
        $anotherUndesiredThread->delete();
        $anotherUndesiredProfilePost->delete();
    }
}