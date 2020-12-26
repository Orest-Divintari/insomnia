<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ReplyFactory;
use Tests\Feature\Search\SearchAllPostsTest;

class SearchAllPostsWithSearchQueryTest extends SearchAllPostsTest
{
    /** @test */
    public function search_all_posts_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);
        $desiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $desiredReply = ReplyFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredThread->id,
        ]);
        $desiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $desiredComment = CommentFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $results = $this->search(
            [
                'q' => $this->searchTerm,
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
    public function search_all_posts_that_were_created_by_a_given_username_given_a_search_term()
    {
        $user = $this->signIn();

        $desiredThread = create(
            Thread::class,
            [
                'body' => $this->searchTerm,
                'user_id' => $user->id,
            ]
        );

        $desiredReply = ReplyFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
        ]);

        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'body' => $this->searchTerm,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $anotherUser->id,
                'body' => $this->searchTerm,
            ]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);
        $undesiredThread = create(
            Thread::class,
            [
                'body' => $this->searchTerm,
                'user_id' => $anotherUser->id,
            ]
        );
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        $results = $this->search([
            'q' => $this->searchTerm,
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

        $desiredThread->delete();
        $undesiredThread->delete();
        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_all_posts_given_a_search_term_and_username_that_were_created_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = create(
            Thread::class,
            [
                'body' => $this->searchTerm,
                'user_id' => $user->id,
            ]
        );
        $desiredReply = ReplyFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
        ]);
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'body' => $this->searchTerm,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $anotherUser->id,
                'body' => $this->searchTerm,
            ]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
            'user_id' => $anotherUser->id,
            'body' => $this->searchTerm,
        ]);
        $undesiredThread = create(
            Thread::class,
            [
                'user_id' => $anotherUser->id,
                'body' => $this->searchTerm,
            ]
        );
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
            'user_id' => $anotherUser->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);
        $anotherUndesiredThread = create(
            Thread::class,
            ['body' => $this->searchTerm]
        );
        $anotherUndesiredReply = ReplyFactory::create([
            'repliable_id' => $anotherUndesiredThread->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'q' => $this->searchTerm,
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

        $desiredThread->delete();
        $undesiredThread->delete();
        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
        $anotherUndesiredThread->delete();
    }
}