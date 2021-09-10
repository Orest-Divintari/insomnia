<?php

namespace Tests\Unit;

use App\Search\ElasticSearchAllPosts;
use App\Search\ElasticSearchIndexFactory;
use App\Search\ElasticSearchProfilePosts;
use App\Search\ElasticSearchTags;
use App\Search\ElasticSearchThreads;
use App\Search\ElasticSearchThreadTitles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ElasticSearchIndexFactoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new ElasticSearchIndexFactory;
    }

    /** @test */
    public function it_returns_elastic_search_threads_index()
    {
        $query = '';
        $type = 'thread';
        $onlyTitle = false;

        $index = $this->factory->create($query, $type, $onlyTitle);

        $this->assertInstanceOf(ElasticSearchThreads::class, $index);
    }

    /** @test */
    public function it_returns_elastic_search_profile_posts_index()
    {
        $query = '';
        $type = 'profile_post';
        $onlyTitle = false;

        $index = $this->factory->create($query, $type, $onlyTitle);

        $this->assertInstanceOf(ElasticSearchProfilePosts::class, $index);
    }

    /** @test */
    public function it_returns_elastic_all_posts_index()
    {
        $query = '';
        $type = '';
        $onlyTitle = false;

        $index = $this->factory->create($query, $type, $onlyTitle);

        $this->assertInstanceOf(ElasticSearchAllPosts::class, $index);
    }

    /** @test */
    public function it_returns_elastic_search_threads_by_title_index()
    {
        $query = $this->faker()->sentence();
        $type = '';
        $onlyTitle = true;

        $index = $this->factory->create($query, $type, $onlyTitle);

        $this->assertInstanceOf(ElasticSearchThreadTitles::class, $index);
    }

    /** @test */
    public function it_returns_elastic_search_tags_index()
    {
        $query = '';
        $type = 'tag';
        $onlyTitle = '';

        $index = $this->factory->create($query, $type, $onlyTitle);

        $this->assertInstanceOf(ElasticSearchTags::class, $index);
    }

}