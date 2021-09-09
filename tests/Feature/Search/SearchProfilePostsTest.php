<?php

namespace Tests\Feature\Search;

use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $this->numberOfDesiredProfilePosts = 1;
        $this->numberOfUndesiredProfilePosts = 1;
        $this->numberOfDesiredComments = 1;
        $this->numberOfUndesiredComments = 1;
        $this->totalNumberOfDesiredItems = $this->numberOfDesiredComments + $this->numberOfDesiredProfilePosts;
        $this->totalNumberOfUndesiredItems = $this->numberOfUndesiredComments + $this->numberOfUndesiredProfilePosts;
    }
}