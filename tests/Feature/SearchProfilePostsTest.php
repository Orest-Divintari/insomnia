<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Egulias\EmailValidator\Warning\Comment;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Profiler\Profile;

class SearchProfilePostsTest extends SearchTest
{
    use RefreshDatabase;

    use RefreshDatabase;

    protected $numberOfDesiredProfilePosts;
    protected $numberOfDesiredComments;
    protected $numberOfUndesiredComments;
    protected $numberOfUndesiredProfilePosts;
    protected $totalNumberOfDesiredItems;
    protected $totalNumberOfUndesiredItems;

    public function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'algolia']);
        $this->numberOfDesiredProfilePosts = 1;
        $this->numberOfUndesiredProfilePosts = 1;
        $this->numberOfDesiredComments = 1;
        $this->numberOfUndesiredComments = 1;
        $this->totalNumberOfDesiredItems = $this->numberOfDesiredComments + $this->numberOfDesiredProfilePosts;
        $this->totalNumberOfUndesiredItems = $this->numberOfUndesiredComments + $this->numberOfUndesiredProfilePosts;

    }
    /** @test */
    public function get_the_profile_posts_that_are_created_by_a_given_username()
    {
        $user = $this->signIn();
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
        ],
            $this->numberOfDesiredProfilePosts
        );
        $this->assertCount(
            $this->numberOfDesiredProfilePosts, $results
        );
        $first = $this->numberOfDesiredProfilePosts - 1;
        $this->assertProfilePost($results[$first], $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_comments_that_a_given_username_has_posted()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $desiredProfilePost = create(ProfilePost::class);
        $user = $this->signIn();
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
        ],
            $this->numberOfDesiredComments
        );

        $this->assertCount(
            $this->numberOfDesiredComments,
            $results
        );
        $first = $this->numberOfDesiredComments - 1;
        $this->assertComment(
            $results[$first],
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_by_a_given_username()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $user = $this->signIn();
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $desiredComment = CommentFactory::create([
            'user_id' => $user->id,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost(
            $resultedProfilePost,
            $desiredProfilePost
        );
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $undesiredProfilePost->delete();
        $desiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_on_a_user_profile()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $user = $this->signIn();
        $profileOwner = create(User::class);
        $desiredProfilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment($resultedComment, $desiredComment, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days()
    {
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(ProfilePost::class);
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'lastCreated' => $daysAgo,
        ],
            $this->numberOfDesiredItems
        );

        $this->assertCount(
            $this->numberOfDesiredItems,
            $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment($resultedComment, $desiredComment, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_user_name()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );

        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_on_a_given_user_profile()
    {
        $user = $this->signIn();
        $profileOwner = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );

        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
        ]);
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'lastCreated' => $daysAgo,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
        );

        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_username_on_a_given_user_profile()
    {
        $user = $this->signIn();
        $profileOwner = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
            ]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );

        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }

    /** @test */
    public function search_profile_posts_and_comments_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
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
                'type' => 'profile_post',
                'q' => $this->searchTerm,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );

        $results = collect($results);
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');

        $this->assertComment($resultedComment, $desiredComment, $desiredProfilePost);
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $desiredComment = CommentFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm,
                'lastCreated' => $daysAgo,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment($resultedComment, $desiredComment, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_given_a_search_term()
    {
        $profileOwner = create(User::class);
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'body' => $this->searchTerm,
                'profile_owner_id' => $profileOwner->id,
            ]
        );
        $desiredComment = CommentFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $undesiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_by_a_given_username_given_a_search_term_the_last_given_number_of_days()
    {
        $profileOwner = create(User::class);
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
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
                'profile_owner_id' => $profileOwner->id,
                'body' => $this->searchTerm,
            ]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
                'body' => $this->searchTerm,
            ]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );

        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }
    /**
     * Assert that the resulted profile post is correct
     * and the required relations are loaded
     *
     * @param array $resultedProfilePost
     * @param ProfilePost $desiredProfilePost
     * @return void
     */
    public function assertProfilePost($resultedProfilePost, $desiredProfilePost)
    {
        $this->assertEquals(
            $resultedProfilePost['id'], $desiredProfilePost->id
        );
        $this->assertEquals(
            $resultedProfilePost['poster']['id'], $desiredProfilePost->poster->id
        );
        $this->assertEquals(
            $resultedProfilePost['profile_owner']['id'], $desiredProfilePost->profileOwner->id
        );
    }

    /**
     * Assert that the resulted comment is correct
     * and the required relations are loaded
     *
     * @param array $resultedComment
     * @param Reply $desiredComment
     * @param ProfilePost $desiredProfilePost
     * @return void
     */
    public function assertComment($resultedComment, $desiredComment, $desiredProfilePost)
    {
        $this->assertEquals(
            $resultedComment['id'], $desiredComment->id
        );
        $this->assertEquals(
            $resultedComment['poster']['id'], $desiredComment->poster->id
        );
        $this->assertEquals(
            $resultedComment['repliable']['id'], $desiredProfilePost->id
        );
        $this->assertEquals(
            $resultedComment['repliable']['poster']['id'], $desiredProfilePost->poster->id
        );
        $this->assertEquals(
            $resultedComment['repliable']['profile_owner']['id'], $desiredProfilePost->profileOwner->id,
        );
    }
}
