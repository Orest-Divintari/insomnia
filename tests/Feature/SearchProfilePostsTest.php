<?php

namespace Tests\Feature;

use App\ProfilePost;
use Egulias\EmailValidator\Warning\Comment;
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