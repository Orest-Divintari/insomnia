<?php

namespace Tests\Unit;

use App\Filters\ElasticProfilePostFilters;
use App\Filters\ElasticSearchFilterFactory;
use App\Filters\ElasticThreadFilters;
use App\Filters\FilterChain;
use App\Filters\FilterManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElasticSearchFilterFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected $filterChain;
    protected $filterManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->filterChain = new FilterChain;
        $this->filterManager = new FilterManager($this->filterChain);
    }

    /** @test */
    public function it_returns_filter_manager_with_elastic_thread_filters_when_the_type_is_thread()
    {
        $factory = new ElasticSearchFilterFactory($this->filterManager);

        $filterManager = $factory->create('thread');

        $this->assertContains(ElasticThreadFilters::class, $filterManager->getFilters());
    }

    /** @test */
    public function it_returns_filter_manager_with_elastic_thread_filters_when_the_type_is_tag()
    {
        $factory = new ElasticSearchFilterFactory($this->filterManager);

        $filterManager = $factory->create('tag');

        $this->assertContains(ElasticThreadFilters::class, $filterManager->getFilters());
    }

    /** @test */
    public function it_returns_filter_manager_with_elastic_profile_post_filters_when_the_type_is_profile_post()
    {
        $factory = new ElasticSearchFilterFactory($this->filterManager);

        $filterManager = $factory->create('profile_post');

        $this->assertContains(ElasticProfilePostFilters::class, $filterManager->getFilters());
    }

    /** @test */
    public function it_returns_filter_manager_with_elastic_profile_post__and_thread_filters_when_the_type_is_empty()
    {
        $factory = new ElasticSearchFilterFactory($this->filterManager);

        $filterManager = $factory->create('');

        $this->assertContains(ElasticThreadFilters::class, $filterManager->getFilters());
        $this->assertContains(ElasticProfilePostFilters::class, $filterManager->getFilters());
    }
}