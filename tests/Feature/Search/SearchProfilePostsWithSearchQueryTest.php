<?php

namespace Tests\Feature\Search;

use App\Models\ProfilePost;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SearchableTest;

class SearchProfilePostsWithSearchQueryTest extends TestCase
{
    use RefreshDatabase, SearchableTest;

    /** @test */
    public function search_profile_posts_and_comments_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $desiredProfilePost = ProfilePostFactory::withBody($this->sentence())->create();
        $desiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $totalNumberOfDesiredItems = 2;

        $results = $this->searchJson(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm(),
            ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems,
            $results
        );

        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }

    /** @test */
    public function search_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::withBody($this->sentence())->create();
        $desiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $anotherUndesiredProfilePost = ProfilePostFactory::withBody($this->sentence())->create();
        $undesiredComment = CommentFactory::toProfilePost($anotherUndesiredProfilePost)->create();
        $anotherUndesiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        $totalNumberOfDesiredItems = 2;

        Carbon::setTestNow();
        $results = $this->searchJson(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm(),
                'last_created' => $daysAgo,
            ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }

    /** @test */
    public function search_profile_posts_and_comments_created_by_a_given_username_the_last_given_number_of_days()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $anotherUser = create(User::class);
        $undesiredProfilePost = ProfilePostFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->toProfilePost($undesiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->withBody($this->sentence())
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        Carbon::setTestNow();
        $totalNumberOfDesiredItems = 2;

        $results = $this->searchJson([
            'type' => 'profile_post',
            'q' => $this->searchTerm(),
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_given_a_search_term()
    {
        $profileOwner = create(User::class);
        $desiredProfilePost = ProfilePostFactory::withBody($this->sentence())
            ->toProfile($profileOwner)
            ->create();
        $desiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredProfilePost = ProfilePostFactory::withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $totalNumberOfDesiredItems = 2;

        $results = $this->searchJson([
            'type' => 'profile_post',
            'q' => $this->searchTerm(),
            'profile_owner' => $profileOwner->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }
    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_on_a_user_profile_given_a_search_query()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $profileOwner = create(User::class);
        $desiredProfilePost = ProfilePostFactory::toProfile($profileOwner)
            ->withBody($this->sentence())
            ->create();
        $desiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $numberOfDesiredItems = 2;
        $results = $this->searchJson([
            'type' => 'profile_post',
            'q' => $this->searchTerm(),
            'profile_owner' => $profileOwner->name,
        ],
            $numberOfDesiredItems
        );

        $this->assertCount(
            $numberOfDesiredItems, $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
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
            ->withBody($this->sentence())
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredProfilePost = ProfilePostFactory::toProfile($profileOwner)
            ->withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($undesiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->withBody($this->sentence())
            ->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->withBody($this->sentence())
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        Carbon::setTestNow();
        $totalNumberOfDesiredItems = 2;

        $results = $this->searchJson([
            'type' => 'profile_post',
            'q' => $this->searchTerm(),
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
            'profile_owner' => $profileOwner->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount(
            $totalNumberOfDesiredItems,
            $results
        );
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }
}