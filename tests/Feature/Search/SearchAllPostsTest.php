<?php

namespace Tests\Feature\Search;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchTest;

class SearchAllPostsTest extends SearchTest
{
    use RefreshDatabase;

    protected $numberOfDesiredThreads;
    protected $numberOfDesiredReplies;
    protected $numberOfUndesiredReplies;
    protected $numberOfUndesiredThreads;
    protected $totalNumberOfDesiredItems;
    protected $totalNumberOfUndesiredItems;
    protected $numberOfDesiredProfilePosts;
    protected $numberOfDesiredComments;
    protected $numberOfUndesiredComments;
    protected $numberOfUndesiredProfilePosts;

    public function setUp(): void
    {
        parent::setUp();
        $this->numberOfDesiredProfilePosts = 1;
        $this->numberOfUndesiredProfilePosts = 1;
        $this->numberOfDesiredComments = 1;
        $this->numberOfUndesiredComments = 1;
        $this->numberOfDesiredThreads = 1;
        $this->numberOfUndesiredThreads = 1;
        $this->numberOfDesiredReplies = 1;
        $this->numberOfUndesiredReplies = 1;
        $this->totalNumberOfDesiredItems = $this->numberOfDesiredThreads
         + $this->numberOfDesiredReplies
         + $this->numberOfDesiredProfilePosts
         + $this->numberOfDesiredComments;
        $this->searchTerm = 'iphone';
    }
}