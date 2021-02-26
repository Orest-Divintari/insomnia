<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchProfilePostsTest;

class SearchProfilePostsWithSearchQueryTest extends SearchProfilePostsTest
{
    use RefreshDatabase;

    /** @test */
    public function search_profile_posts_and_comments_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $desiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)->create();
        $desiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();

        $results = $this->search(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)->create();
        $desiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $anotherUndesiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)->create();
        $undesiredComment = CommentFactory::toProfilePost($anotherUndesiredProfilePost)->create();
        $anotherUndesiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();

        Carbon::setTestNow();
        $results = $this->search(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm,
                'last_created' => $daysAgo,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_profile_posts_and_comments_created_by_a_given_username_the_last_given_number_of_days()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $anotherUser = create(User::class);
        $undesiredProfilePost = ProfilePostFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredComment = CommentFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->toProfilePost($undesiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->withBody($this->searchTerm)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        Carbon::setTestNow();

        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();

    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_given_a_search_term()
    {
        $profileOwner = create(User::class);
        $desiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)
            ->toProfile($profileOwner)
            ->create();
        $desiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)
            ->create();
        $undesiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($undesiredProfilePost)
            ->create();

        
        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'profile_owner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_by_a_given_username_given_a_search_term_the_last_given_number_of_days()
    {
        $profileOwner = create(User::class);
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->withBody($this->searchTerm)
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredProfilePost = ProfilePostFactory::toProfile($profileOwner)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($undesiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->withBody($this->searchTerm)
            ->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->withBody($this->searchTerm)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        Carbon::setTestNow();

        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
            'profile_owner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }
}